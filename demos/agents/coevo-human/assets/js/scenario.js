
class Scenario {
  constructor(data) {
    this.name = data.name;
    this.dims = data.dims
    this.time = data.defaults.time;
    this.color = data.defaults.color;
    this.simulationSteps = this.time * 60;
    this.constraints = data.constraints
    this.speedFactor = 1;
    this.objects = []
    this.goals = []
    this.endSimulationPos = [];
    this.init(data.objects);
  }
  render() {
    background(this.color);
    this.objects.forEach(o => { o.display(); });
    this.goals.forEach(g => { g.display(); });
  }

  renderTo(p) {
    p.background(this.color)
    this.objects.forEach(o => { o.displayTo(p) })
    this.goals.forEach(g => { g.displayTo(p) })
  }

  showMinimized(x,y,minFactor) {
    this.objects.forEach(o => { o.displayMin(x,y,minFactor);});
    this.goals.forEach(g => { g.displayMin(x,y,minFactor);});
  }
  
  getReward() {
    let reward = 0;
    let maxReward = [];
    this.goals.forEach(g => { 
      let rewards = g.getReward(this.objects);
      reward += rewards['reward']
      maxReward.push(rewards['max-reward']);
    });
    let minReward = min(maxReward)
    if(minReward == max(maxReward)) minReward = 0;
    reward = map(reward,minReward,max(maxReward),0,1);
    if(reward < 0.001) return 0;
    return round(reward,2);
  }

  getEndSimulation(){
    this.objects.forEach(o => { 
      o.setEndSimulationPos({ 'x':o.body.position.x , 'y':o.body.position.y}); 
    });
  }

  init(elements) {
    let finalPos = []
    this.objects.forEach(e => {
      e.deleteBody();
      if(e.getEndSimulationPos() != undefined) finalPos.push(e.getEndSimulationPos());
    });
    this.objects = [];
    this.goals = [];
    elements.forEach(e => {
      switch (e.type) {
        case "Init":
          break;
        case "Ball":
          this.objects.push(new Ellipse(e));
          break;
        case "Restrictor":
          this.goals.push(new Restrictor(e));
          break;
        case "Container":
          this.goals.push(new Container(e));
          break;
        case "Poly":
          //objects.push(new Polygon(e));
          break;
        case "Rectangle":
          this.objects.push(new Rectangle(e));
          break;
      }
    });
    let i = 0;
    if(finalPos.length > 0) this.objects.forEach( o => { o.setEndSimulationPos(finalPos[i]); i++; });
  }

  getCreatureDimensions() {
    return this.scale;
  }

}