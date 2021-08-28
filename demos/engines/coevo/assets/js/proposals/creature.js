class Creature {
  constructor(x, y, rotation, w, h, i, isStatic) {
    this.x = x;
    this.y = y;
    this.w = w;
    this.h = h;
    this.i = i;
    this.angle = rotation;
    let options = {
      isStatic: isStatic,
      friction: 0,
      restitution: 1,
      label: "pCreature",
      angle: this.angle
    }
    this.color = [20, i * 20, i * 20, 200];
    this.body = Matter.Bodies.rectangle(this.x, this.y, this.w, this.h, options);
  }

  getBody() {
    return this.body;
  }

  getPosition() {
    return this.body.position.x;
  }

  show() {
    if (this.body != null) {
      let pos = this.body.position;
      let angle = this.body.angle;

      push();
      fill(0, 0, 0);
      translate(pos.x, pos.y);
      rotate(angle);
      rect(-this.w / 2, -this.h / 2, this.w, this.h);
      fill(255);
      pop();
    }
  }

  showMin(x, y, m) {
    if (this.body != null) {
      let pos = this.body.position;
      let angle = this.body.angle;
      push();
      fill(this.color[0], this.color[1], this.color[2], this.color[3]);
      translate(x + pos.x / m.x, y + pos.y / m.y);
      rotate(angle);
      rect(-this.w / 2 / m.x, -this.h / 2 / m.y, this.w / m.x, this.h / m.y);
      fill(255);
      pop();
    }
  }

  activate() {
    Matter.World.add(world, this.body);
  }
  deactivate() {
    Matter.World.remove(world, this.body);
  }

  getColor(color) {
    return this.color;
  }

  changeColor(color) {
    this.color = color;
  }

  updatePosition(posX, posY) {
    let shape = this.body.vertices;

    this.body.position.x = posX;
    this.body.position.y = posY;

    this.body.positionPrev.x = this.body.position.x;
    this.body.positionPrev.y = this.body.position.y;

    shape[0].x = this.body.position.x + cos(this.angle) * (-1 * this.w / 2) - sin(this.angle) * (-1 * this.h / 2);
    shape[1].x = this.body.position.x + cos(this.angle) * (1 * this.w / 2) - sin(this.angle) * (-1 * this.h / 2);
    shape[2].x = this.body.position.x + cos(this.angle) * (-1 * this.w / 2) - sin(this.angle) * (1 * this.h / 2);
    shape[3].x = this.body.position.x + cos(this.angle) * (1 * this.w / 2) - sin(this.angle) * (1 * this.h / 2);

    let min = shape[0].x;
    let max = shape[0].x;
    for (let i = 0; i < shape.length; i++) {
      if (shape[i].x < min) min = shape[i].x;
      if (shape[i].x > max) max = shape[i].x;
    }

    this.body.bounds.min.x = min;
    this.body.bounds.max.x = max;

    shape[0].y = this.body.position.y + sin(this.angle) * (-1 * this.w / 2) + cos(this.angle) * (-1 * this.h / 2);
    shape[1].y = this.body.position.y + sin(this.angle) * (1 * this.w / 2) + cos(this.angle) * (-1 * this.h / 2);
    shape[2].y = this.body.position.y + sin(this.angle) * (-1 * this.w / 2) + cos(this.angle) * (1 * this.h / 2);
    shape[3].y = this.body.position.y + sin(this.angle) * (1 * this.w / 2) + cos(this.angle) * (1 * this.h / 2);

    min = shape[0].y;
    max = shape[0].y;
    for (let i = 0; i < shape.length; i++) {
      if (shape[i].y < min) min = shape[i].y;
      if (shape[i].y > max) max = shape[i].y;
    }

    this.body.bounds.min.y = min;
    this.body.bounds.max.y = max;
  }


}







