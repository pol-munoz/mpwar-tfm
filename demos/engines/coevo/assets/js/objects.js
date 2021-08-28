class Shape {
  constructor(type, color, isGoal) {
    this.type = type;
    this.color = color;
    this.isGoal = isGoal;
  }
  getColor() {
    return "#" + hex(this.color.r, 2) + hex(this.color.g, 2) + hex(this.color.b, 2);
  }

  isGoalObject(){
    return this.isGoal;
  }
  
}

class Rectangle extends Shape {
  constructor(element) {
    super(element.type, element.color,element.isGoal);
    this.w = element.w;
    this.h = element.h;
    let isStatic = true;

    if (element.density == 1) isStatic = false;
    let defaults = {
      isStatic: isStatic,
      slope: 0,
      restitution:0,
      intertia:Infinity,
      restitution: 0,
      friction: 0,//0.2,
      angle: element.rotation
    };
    this.body = Matter.Bodies.rectangle(element.position[0] + cos(element.rotation)*this.w*0.5 + sin(element.rotation)*this.h*0.5, element.position[1] + sin(element.rotation)*this.w*0.5 + cos(element.rotation)*this.h*0.5, this.w, this.h, defaults)
    Matter.World.add(world, this.body);
    this.initPos = createVector(this.body.position.x,this.body.position.y);

  }

  setEndSimulationPos(position){
    this.endSimulationPos = createVector(position.x,position.y);
  }

  getEndSimulationPos(){
    return this.endSimulationPos;
  }
 
  deleteBody() {
    Matter.World.remove(world, this.body);
  }
  display() {
    this.displayTo(window)
  }

  displayTo(p) {
    if (this.body != null) {
      let pos = this.body.position;
      let angle = this.body.angle;
      p.push();
      p.noStroke()
      p.rectMode(CENTER)
      p.translate(pos.x, pos.y);
      p.rotate(angle);
      p.fill(this.getColor());
      p.rect(0, 0, this.w, this.h);
      p.pop();
    }
  }

  displayMin(x,y,minFactor) {
    if (this.body != null) {
      let pos = createVector(x+this.body.position.x/minFactor.x,y+this.body.position.y/minFactor.y) ;
      let angle = this.body.angle;
      push();
      noStroke()
      rectMode(CENTER)
      translate(pos.x, pos.y);
      rotate(angle);
      fill(this.getColor());
      rect(0, 0, this.w/minFactor.x, this.h/minFactor.y);
      pop();
    }
  }
}

class Ellipse extends Shape {
  constructor(element) {
    super(element.type, element.color, element.isGoal);
    this.radius = element.w * 0.5 //element.radius;   
    let isStatic = true;
    this.endSimulationPos;
    if (element.density == 1) isStatic = false;
    let defaults = {
      isStatic: isStatic,
      slope: 0.1,
      restitution: 0.1,
      friction: 0.05,
      angle: element.rotation
    };
    this.body = Matter.Bodies.circle(element.position[0]+this.radius, element.position[1]+this.radius, this.radius, defaults)
    Matter.World.add(world, this.body);
    this.initPos =  createVector(this.body.position.x,this.body.position.y);
  }
 
  setEndSimulationPos(position){
    this.endSimulationPos = createVector(position.x,position.y);
  }

  getEndSimulationPos(){
    return this.endSimulationPos;
  }

 
  deleteBody() {
    Matter.World.remove(world, this.body);
  }

  display() {
    this.displayTo(window)
  }

  displayTo(p) {
    if (this.body != null) {
      let pos = this.body.position;
      let angle = this.body.angle;
      p.push();
      p.rectMode(CENTER)
      p.translate(pos.x, pos.y);
      p.rotate(angle);
      p.noFill();
      p.stroke(this.getColor());
      p.strokeWeight(2);
      p.ellipse(0, 0, this.radius * 2, this.radius * 2);
      p.strokeWeight(0.5);
      p.line(0, 0, this.radius, 0);
      p.pop();
    }
  }
  displayMin(x,y,minFactor) {
    if (this.body != null) {
      let pos = createVector(x+this.body.position.x/minFactor.x,y+this.body.position.y/minFactor.y) ;
      let angle = this.body.angle;
      push();
      rectMode(CENTER)
      translate(pos.x, pos.y);
      rotate(angle);
      noFill();
      stroke(this.getColor());
      strokeWeight(2);
      ellipse(0, 0, this.radius * 2/minFactor.x, this.radius * 2/minFactor.y);
      strokeWeight(0.5);
      line(0, 0, this.radius/minFactor.x, 0);
      pop();
    }
  }
}
