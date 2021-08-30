
let fastSimulate = false;
let pauseExperiment = false;
let autoGen = false;
let showLegend = false;

const controllerStates = {
  SIMULATE: 'simulate',
  GENERATE: 'generate',
  SHAPELAB: 'shapeLab',
  DEMO: 'demo'
}

const editionMode = {
  FOLLOW_MOUSE: 'follow',
  SINGLE_EDIT: 'single',
  AREA_EDIT: 'area'
}

function keyPressed() {
  // General keys
  if (key === 's' || key === 'S') {
    // S switches to / from ShapeLab
    finishEditionMode();
    fastSimulate = false;
    if (currentState === controllerStates.SHAPELAB) {
      shapeLab.closeShapeLab()
      currentState = controllerStates.SIMULATE

      creatures[cCreature].init()
      creatures[cCreature].activate()
      resetScenario()
      logNewEvent('USER_STARTED_SIMULATION')
    } else {
      if (cCreature < creatures.length) {
        currentState = controllerStates.SHAPELAB
        creatures[cCreature].deactivate();
        creatures[cCreature].init()
        shapeLab.initCreature();
        logNewEvent('USER_STARTED_SHAPELAB')
      }
    }
    if (!roomAdmin) {
      updateUserStatus(currentState)
    }
  }

  if (key === 'g' || key === 'G') {
    autoGen = !autoGen;
  }
  if (key === 'f' || key === 'F') {
    fastSimulate = !fastSimulate
  }

  // State-specific keys
  switch (currentState) {
    case controllerStates.SIMULATE:
      if (key === 'p' || key === 'P') {
        pauseExperiment = !pauseExperiment
        updateView()
      }
      break
    case controllerStates.SHAPELAB:

      if (key === 'ArrowLeft' && roomAdmin) {
        displayPreviousCreature()
      }
      if (key === 'ArrowRight' && roomAdmin) {
        displayNextCreature()
      }
      if (key === 'e' || key === 'E') {
        manageEditionMode();
      }
      if ((key === 'n' || key === 'N') && roomAdmin) {
        createNewCreature()
      }
      if (key === 'c' || key === 'C') {
        clearCurrentCreature()

      }
      if (shapeLab.isEditing) {
        if (key === '+') {
          addNewPiece()
        }
        if (key === '-') {
          removeLastPiece()
        }


        if (key === 'Backspace') {
          removeLastPiece()
        }
        if (key === 'Enter') {
          console.log('Edit completed')
          finishEditionMode();
        }
      }
      break
  }
}

function mouseMoved() {
  switch (currentState) {
    case controllerStates.SHAPELAB:
      shapeLab.followMouse();
      break;
  }
}

function mousePressed() {
  switch (currentState) {
    case controllerStates.SHAPELAB:
      if (shapeLab.isEditing) {
        addNewPiece();
      }
      break;
  }
}

function updateUserStatus(status) {
  sendAgentMessage({meta: meta.STATUS, status}, [KunlaboAction.MESSAGE])
}


function canEdit() {
  return (roomAdmin && cCreature > 0 || !roomAdmin && yourTurn)
}

// Kind of redundant functions below. They abstract the view.js button binding from controller logic
function displayNextCreature() {
  shapeLab.finishEditing()
  if (cCreature < creatures.length - 1) {
    cCreature++;
  } else {
    cCreature = 0
  }
  shapeLab.initCreature();

  if (roomAdmin) {
    updateAdminUI()
  }
}

function displayCreature(n) {
  shapeLab.finishEditing()
  cCreature = n
  shapeLab.initCreature();

  if (roomAdmin) {
    updateAdminUI()
  }
}


function displayPreviousCreature() {
  shapeLab.finishEditing()
  if (cCreature > 0) {
    cCreature--;
  } else {
    cCreature = creatures.length - 1;
  }
  shapeLab.initCreature();

  if (roomAdmin) {
    updateAdminUI()
  }
}


function manageEditionMode() {
  if (canEdit()) {
    if (shapeLab.isEditing) {
      finishEditionMode()
      if (!roomAdmin) {
        updateUserStatus(currentState)
      }
      logNewEvent('USER_FINISHED_EDITING', creatures[cCreature], true)
    } else {
      activateEditionMode()
      if (!roomAdmin) {
        updateUserStatus(currentState + " (edit)")
      }
      logNewEvent('USER_STARTED_EDITING')
    }
  } else {
    logNewEvent('USER_TRIED_EDITING_BUT_UNABLE')
  }
}
function activateEditionMode() {
  shapeLab.startEditing();
  updateView();
}

function finishEditionMode() {
  shapeLab.finishEditing();
  updateView();
}

function addNewPiece() {
  activateEditionMode();
  shapeLab.addPiece();
  logNewEvent('USER_ADDED_NEW_PIECE', {id: Date.now(), dna: shapeLab.roboArm.convert2Genes()}, true)
}
function removeLastPiece() {
  activateEditionMode();
  shapeLab.removePiece();
  logNewEvent('USER_REMOVED_LAST_PIECE', {id: Date.now(), dna: shapeLab.roboArm.convert2Genes()}, true)
}
function createNewCreature() {
  shapeLab.newCreature();
  shapeLab.clearPieces();
  activateEditionMode();

  if (roomAdmin) {
    updateAdminUI()
  }
}
function cloneCreature() {
  shapeLab.cloneCreature();
  activateEditionMode();

  if (roomAdmin) {
    updateAdminUI()
  }
}
function clearCurrentCreature() {
  if (canEdit()) {
    shapeLab.clearPieces();
    activateEditionMode();
    logNewEvent('USER_CLEARED_CREATURE')
  } else {
    logNewEvent('USER_TRIED_CLEARING_CREATURE_BUT_UNABLE')
  }
}

function pushCurrentCreature() {
  switch (roomType) {
    case roomTypes.TURNS:
      shapeLab.pushTo(creatures[cCreature], 'creatures', 0)
          
      if (!roomAdmin) {
        sendAgentMessage({meta: meta.TURN})
      } else {
        sendEngineMessage({meta: meta.TURN})
      }
      break
    case roomTypes.OVERLAP:
      shapeLab.pushTo(creatures[cCreature], 'suggestions', 0)
      break
    case roomTypes.OPTIONS:
      shapeLab.pushTo(creatures[cCreature], 'suggestions', cCreature - 1)
      break
  }
}
function pullCurrentCreature() {
  shapeLab.pushTo(creatures[0], 'creatures', cCreature)
}

function acceptSuggestion(i) {
  logNewEvent('USER_ACCEPTED_SUGGESTION', {channel: i, suggestion: suggestions[i]}, true)
  shapeLab.pushTo(suggestions[i], 'creatures', 0)

  if (!roomAdmin) {
    removeSuggestion(i)
    sendAgentMessage({meta: meta.ACCEPT, index: i})
  }
}

function rejectSuggestion(i) {
  logNewEvent('USER_REJECTED_SUGGESTION', {channel: i, suggestion: suggestions[i]}, true)
  
  if (!roomAdmin) {
    removeSuggestion(i)
    sendAgentMessage({meta: meta.REJECT, index: i})
  }
}
function endCurrentTurn() {
  logNewEvent('USER_FINISHED_TURN')

  if (!roomAdmin) {
    sendAgentMessage({meta: meta.TURN})
  } else {
    sendEngineMessage({meta: meta.TURN})
  }
  yourTurn = false
  shapeLab.finishEditing()
  updateView()
}

function updateAiChange() {
  logNewEvent('USER_UPDATED_AI_CHANGE', this.checked)
  if (!roomAdmin) {
    sendAgentMessage({meta: meta.AI, change: this.checked}, [KunlaboAction.MESSAGE, KunlaboAction.PERSIST])
  }
}
function updateAiSimilarity() {
  logNewEvent('USER_UPDATED_AI_SIMILARITY', this.value)
  if (!roomAdmin) {
    sendAgentMessage({meta: meta.AI, similarity: this.value}, [KunlaboAction.MESSAGE, KunlaboAction.PERSIST])
  }
}