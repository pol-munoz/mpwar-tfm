class Container extends Shape {
  constructor(element) {
    super(element.type, element.color);
    this.pos = element.position;
    this.w = element.w;
    this.h = element.h;
    this.rotation = element.rotation;
  }
  display() {
    this.displayTo(window)
  }

  displayTo(p) {
    p.push();
    p.rectMode(CENTER)
    p.translate(this.pos[0] + this.w * 0.5, this.pos[1] + this.h * 0.5);
    p.rotate(this.rotation);
    p.stroke(this.getColor());
    p.strokeWeight(2);
    let c = p.color(this.getColor());
    c.setAlpha(125);
    p.fill(c);
    p.rect(0, 0, this.w, this.h);
    p.pop();
  }

  displayMin(x,y,minFactor) {
    push();
    rectMode(CENTER)
    translate(x+this.pos[0]/minFactor.x + this.w/minFactor.x * 0.5, y+this.pos[1]/minFactor.y + this.h/minFactor.y * 0.5);
    rotate(this.rotation);
    stroke(this.getColor());
    strokeWeight(2);
    let c = color(this.getColor());
    c.setAlpha(125);
    fill(c);
    rect(0, 0, this.w/minFactor.x, this.h/minFactor.y);
    pop();
  }

  getReward(elements) {
    let reward = 0;
    let totalReward = 0;
    let rewards = [];
    //r = 1 - min(distNoIntervention, distance)/ distNoIntervention
 
    elements.forEach(e => {
      if(e.isGoalObject()){
        if (this.checkColor(e.color)) {
          totalReward++;
          if (this.contains(e)) {
            rewards.push(10)
          } else {
            let distance = this.isCloserToCenter(e);
            let noInterventionDistance = this.isCloserToCenter(e, e.getEndSimulationPos());
            if (distance < noInterventionDistance) {
              rewards.push(1 - distance / noInterventionDistance);
            } else {
              rewards.push(0);
            }
          }
        }
      }
    });
    rewards.forEach(r => { reward += r });
    return {"reward":pow(reward,2),
            "max-reward":pow(10*totalReward,2)};
  }

  checkColor(otherColor) {
    if (this.color.r == otherColor.r &&
      this.color.g == otherColor.g &&
      this.color.b == otherColor.b) return true;
    return false;
  }


  isCloserToCenter(o, finalPos) {
    let pos = finalPos;
    if (finalPos == null) {
      pos = o.body.position;
    }
    let w;
    let h;
    switch (o.type) {
      case 'Ball': w = o.radius; h = w; break;
      case 'Rectangle': w = o.w; h = o.h; break;
    }
    return Math.sqrt(Math.pow(pos.x - this.pos[0], 2) + Math.pow(pos.y - this.pos[1], 2));
  }

  contains(o) {
    let pos = o.body.position;
    let w;
    let h;
    switch (o.type) {
      case 'Ball': w = o.radius; h = w; break;
      case 'Rectangle': w = o.w; h = o.h; break;
    }
    return (((pos.x + w) > this.pos[0]) && //esquerra
      ((pos.x - w) < (this.pos[0] + this.w)) &&  //dreta
      ((pos.y + h) > (this.pos[1])) &&  //up
      ((pos.y - h) < (this.pos[1] + this.h))); //down
  }
}

class Restrictor extends Shape {
  constructor(element) {
    super(element.type, element.color);
    this.pos = element.position;
    this.w = element.w;
    this.h = element.h;
    this.rotation = element.rotation;
    this.worstReward = 0;
  }
  display() {
    this.displayTo(window)
  }


  displayTo(p) {
    p.push();
    p.rectMode(CENTER)
    p.translate(this.pos[0] + this.w * 0.5, this.pos[1] + this.h * 0.5);
    p.rotate(this.rotation);
    p.stroke(this.getColor());
    p.strokeWeight(2);
    let c = p.color(this.getColor());
    c.setAlpha(125);
    p.fill(c);
    p.rect(0, 0, this.w, this.h);
    p.pop();
  }


  displayMin(x,y,minFactor) {
    push();
    rectMode(CENTER)
    translate(x+this.pos[0]/minFactor.x + this.w/minFactor.x * 0.5, y+this.pos[1]/minFactor.y + this.h/minFactor.y * 0.5);  rotate(this.rotation);
    stroke(this.getColor());
    strokeWeight(2);
    let c = color(this.getColor());
    c.setAlpha(125);
    fill(c);
    rect(0, 0, this.w/minFactor.x, this.h/minFactor.y);
    pop();
  }

  

  getReward(elements) {
    let reward = 0;
    let totalReward = 0;
    elements.forEach(e => {
      if(e.isGoalObject()){
        totalReward++;
        if (this.contains(e)) {
          reward--;
        }
      }
    });
  
    reward = -(pow(reward,2));
   
    if(reward<this.worstReward){
      this.worstReward = reward;
    }
    return {"reward":this.worstReward,
            "max-reward":-pow(totalReward,2)};
  }

  checkColor(otherColor) {
    if (this.color.r == otherColor.r &&
      this.color.g == otherColor.g &&
      this.color.b == otherColor.b) return true;
    return false;
  }

  contains(o) {
    let pos = o.body.position;
    let w;
    let h;
    switch (o.type) {
      case 'Ball': w = o.radius; h = w; break;
      case 'Rectangle': w = o.w; h = o.h; break;
    }
    return (((pos.x + w) > this.pos[0]) && //esquerra
      ((pos.x - w) < (this.pos[0] + this.w)) &&  //dreta
      ((pos.y + h) > (this.pos[1])) &&  //up
      ((pos.y - h) < (this.pos[1] + this.h))); //down
  }
}