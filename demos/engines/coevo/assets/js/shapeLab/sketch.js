let dataScenario;
let currentState;
let lastState;
let scenario;
let shapeLab;

let creatures = [];
let cCreature = 0;
let engine;
let world;
let timer = 0;

let userColor = {
  r: 0,
  g: 101,
  b: 255
}
let iaColor = {
  r: 255,
  g: 154,
  b: 0
}

function preload() {
  let url = 'assets/scenarios/' + scenarioName
  dataScenario = loadJSON(url)
}

function setup() {
  startScenario(dataScenario.objects);
  currentState = controllerStates.SHAPELAB;
}

function generateLabelsFor(uid) {
  let hash = 0

  for (let i = 0; i < uid.length; i++) {
    let char = uid.charCodeAt(i)
    hash = ((hash << 5) - hash) + char
    hash |= 0; // Convert to 32bit integer
  }

  let color = abs(hash) % 0x1000000
  userColor = {
    r: color  >> 16,
    g: (color >> 8) & 0xff,
    b: color & 0xff
  }

  iaColor = {
    r: 255 - userColor.r,
    g: 255 - userColor.g,
    b: 255 - userColor.b
  }
}

function draw() {
  switch (currentState) {
    case controllerStates.SIMULATE:
      if (!pauseExperiment) {
        if (cCreature < creatures.length) {
          let end = simulateScenario();
          miniStats();
          creatures[cCreature].show(!roomAdmin  && cCreature === 0 && yourTurn  || roomAdmin && cCreature > 0);
          if (end) endScenario();
        }
      }
      break;

    case controllerStates.SHAPELAB:
      scenario.render()
      renderExtraCreatures()
      shapeLab.run();
      miniStats();
      break;
  }

  renderLabels()
  if (currentState != lastState) {
    lastState = currentState;
    updateView();
    resetScenario();
  }
}

function renderExtraCreatures() {
  if (cCreature > 0 && roomAdmin) {
    creatures[0].show(false, 100)
  }
  switch (roomType) {
    case roomTypes.TURNS:
      break
    case roomTypes.OVERLAP:
      if (suggestions.length > 0 && suggestions[0]) {
        suggestions[0].show(roomAdmin, 100);
      }
      break
    case roomTypes.OPTIONS:
      break
  }
}

function renderLabels() {
  let x = width - 60
  push()
  noStroke()
  fill(userColor.r, userColor.g, userColor.b)
  rect(x, 10, 50, 25, 15)
  fill(iaColor.r, iaColor.g, iaColor.b)
  rect(x, 40, 50, 25, 15)

  let t = 255

  if (avgLevel(userColor) > 200) {
    t = 0
  }
  textAlign(CENTER, CENTER)
  fill(t, t, t)
  text('YOU', x + 25, 23)

  t = 255
  if (avgLevel(iaColor) > 200) {
    t = 0
  }
  fill(t, t, t)
  text(roomAdmin ? 'USER' : 'IA', x + 25, 53)
  pop()
}

function avgLevel(col) {
  return (col.r + col.g + col.b) / 3
}

function startScenario() {
  engine = Matter.Engine.create();
  Matter.Resolver._restingThresh = 0.001
  world = engine.world;
  let canvas = createCanvas(dataScenario.dims[0], dataScenario.dims[1]);
  canvas.parent('canvasContainer');
  scenario = new Scenario(dataScenario);
  firstCreatureCreation();
  resetScenario();
  shapeLab = new ShapeLab(dataScenario.constraints);
  shapeLab.initCreature();
  currentState = controllerStates.SIMULATE;
}



function simulateScenario() {
  if (!fastSimulate) {
    Matter.Engine.update(engine, 30.0);
    scenario.render();
    if (frameCount % 60 == 0 && timer > 0) { timer--; }
    if (timer == 0) return true;
  } else {
    for (let i = 0; i < scenario.simulationSteps / scenario.speedFactor; i++) {
      Matter.Engine.update(engine, 30.0 * scenario.speedFactor);
    }
    scenario.render();
    return true;
  }
  return false;
}

function endScenario() {
  let fitness = scenario.getReward()
  logNewEvent('SIMULATION_ENDED_NATURALLY', fitness)
  creatures[cCreature].setFitness(fitness);
  resetScenario();
  creatures[cCreature].deactivate();
  creatures[cCreature].init();
  currentState = controllerStates.SHAPELAB;
  shapeLab.initCreature();

  updateUserStatus(currentState)
  /*
  cCreature++;
  if (cCreature < creatures.length) {
    creatures[cCreature].activate(cCreature);
  } else {
    cCreature--;
    currentState = controllerStates.SHAPELAB;
    creatures[cCreature].deactivate();
    shapeLab.initCreature();
  }
  */
}


function resetScenario() {
  scenario.init(dataScenario.objects)
  timer = scenario.time
}

function miniStats() {
  let tS = 12;
  let spaceCounter = 2;
  let posX = 15;
  push();
  fill(50);
  textSize(tS);
  if (roomAdmin) {
    text('Shape: ' + (cCreature + 1) + "/" + creatures.length + (cCreature == 0 ? ' (User)' : ''), posX, tS * spaceCounter);
    spaceCounter++
  }
  text('Fitness: ' + creatures[cCreature].getFitness(), posX, tS * spaceCounter);
  spaceCounter++
  if (currentState == controllerStates.SIMULATE) {
    tS = 10;
    textSize(tS);
    spaceCounter++;
    text('Time: ' + timer, posX, tS * spaceCounter);
    spaceCounter++;
    text('Fast Simulation:' + fastSimulate, posX, tS * spaceCounter);
    spaceCounter++;
  }
  pop();
}