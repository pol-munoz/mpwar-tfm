class Supercreature {
  constructor(constraints, dna, id) {

    if (id === undefined) {
      this.id = Date.now();
    } else {
      this.id = id
    }
    this.x = constraints.initialPosition[0];
    this.y = constraints.initialPosition[1];
    this.w = constraints.pieceDimension[0];
    this.h = constraints.pieceDimension[1];
    this.color = constraints.color;
    this.parts = [];
    this.partsCom = [];
    this.compound;
    this.isStatic = true;
    this.fitness = null;
    if (constraints.density == 1) {
      this.isStatic = false;
    }
    this.defineDNA(dna)
  }

  static fromRemote(data) {
    let creature = new Supercreature({
      initialPosition: [data.x, data.y],
      pieceDimension: [data.w, data.h],
      color: data.color
    }, DNA.fromRemote(data.dna), data.id)

    creature.isStatic = data.isStatic

    return creature
  }

  setFitness(f) {
    this.fitness = f;
  }
  getFitness() {
    if (this.fitness == null) return '-'
    return this.fitness;
  }

  defineDNA(dna) {
    if (dna == null) {
      this.dna = new DNA();
    } else {
      this.dna = dna;
    }
  }

  getDNA() {
    return this.dna;
  }

  init() {
    this.parts = []
    this.partsCom = [];
    let ofsetX = 0;
    let ofsetY = 0;
    let separation = 1;
    for (let i = 0; i < this.dna.genes.length; i++) {
      let angle = this.dna.getGenome(i).gene;
      if (i > 0) {
        let angleP = this.dna.getGenome(i - 1).gene;
        ofsetX += separation * cos(angle) * this.w / 2 + separation * cos(angleP) * this.parts[i - 1].w / 2;
        ofsetY += separation * sin(angle) * this.w / 2 + separation * sin(angleP) * this.parts[i - 1].w / 2;

      } else {
        ofsetX += separation * cos(angle) * this.w / 2;
        ofsetY += separation * sin(angle) * this.w / 2;
      }
      this.parts.push(new Creature(this.x + ofsetX, this.y + ofsetY, angle, this.w, this.h, i, this.isStatic));
      this.partsCom.push(this.parts[i].getBody());
    }

    this.compound = Matter.Body.create({
      parts: this.partsCom,
      restitution: 0,
      friction: 0,
      //inertia : Infinity,
      isStatic: this.isStatic,
    });
  }
  copy(dna) {
    return new Supercreature(this.x, this.y, dna);
  }
  getBody() {
    return this.parts[0].getBody();
  }
  getPosition() {
    return this.compound.position;

  }

  show(isUser, opacity) {
    this.showTo(window, isUser, opacity)
  }
  showTo(p, isUser, opacity = 255) {
    if (this.partsCom != null) {
      let color = isUser ? userColor : iaColor
      for (let i = 1; i < this.partsCom.length + 1; i++) {
        let v = this.partsCom[i];
        if (v != null) {
          let pos = v.position;
          let angleBody = v.angle;
          p.push();
          //fill(this.parts[i-1].color[0],this.parts[i-1].color[1],this.parts[i-1].color[2],this.parts[i-1].color[3])
          //fill(this.color['r'] * 255, this.color['g'] * 255, this.color['b'] * 255, 200);
          p.fill(color.r, color.g, color.b, opacity);
          p.translate(pos.x, pos.y);
          p.stroke(255, 255, 255, 40);
          p.rotate(angleBody + this.compound.angle);
          //rotate(angleBody);
          p.rect(-this.w / 2, -this.h / 2, this.w, this.h);
          p.pop();
        }
      }
    }
  }

  activate() {
    Matter.World.add(world, this.compound);
  }

  deactivate() {
    for (let i = 0; i < this.parts.length; i++) {
      this.parts[i].deactivate();
    }
    Matter.World.remove(world, this.compound);
  }
}