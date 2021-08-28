
class ShapeLab{
  constructor(constraints){
    this.tS = 12;
    this.constraints = constraints
    this.creatureDimensions = createVector(constraints.pieceDimension[0], constraints.pieceDimension[1])
    this.roboArm;
    this.alignRight = 10;
    this.initPos = createVector(constraints.initialPosition[0], constraints.initialPosition[1]);
    this.yoinkRadius = 15;
    this._lastMouse = {x: 0, y: 0};
    this._isEditing = false;
    this._isDragging = false;
    this._isYoinking = false;
    this._isLastYoinked = false;
    this._yoinked = -1;
    this.hasChanges = false;
  }

  get mode(){ return this._mode;}
  set mode(mode){ this._mode = mode; }
  get isEditing(){ return this._isEditing;}
  run(){
    if(!shapeLab.isEditing && canEdit()){
      this.dragAndDropShape();
    }
    this.show();
  }

  dragAndDropShape(){
     // If the user is pressing the mouse button
     if (mouseIsPressed) {
      // One-time configurations (as this will only be called once if the user holds the button down)
      if (!this._isDragging) {
        // Configuration of the yoinking logic, find out which "node" is the closest to the mouse location, 
        // and if it's close enough to be considered a direct interaction
        this._yoinked = this.roboArm.getClosest(mouseX, mouseY)
        this._isYoinking = this.roboArm.getDistanceFromJoint(this._yoinked, mouseX, mouseY) <= this.yoinkRadius;

        // Take into account the "last" node (the very end)
        if (!this._isYoinking && this.roboArm.isClosestToEnd(mouseX, mouseY, this.yoinkRadius)) {
          this._isLastYoinked = true;
          this._isYoinking = true;
        }

        if (this._isYoinking) {
          let y = this._yoinked

          if (!this._isLastYoinked) {
            y--
          }

          logNewEvent('USER_STARTED_DRAGGING', { position: y, gene: creatures[cCreature].getDNA().getGenome(y) })
        }
      }

      // Per-frame yoinking logic
      if (this._isYoinking) {
        this.roboArm.yoink(this._yoinked, mouseX, mouseY, this._isLastYoinked, getShapeLabBrush() === shapeLabBrushes.MOVE)
        this.hasChanges = true

        if (this._isLastYoinked) {
          this.roboArm.updateLast(mouseX,mouseY);
        }
      }

      // Raise the flag signaling the user is dragging, avoiding the repetition of one-time configurations
      this._isDragging = true;
    } else {
      // Once the user stops dragging, we lower all flags

      if (this._isYoinking) {
        this.updateProposal()
        logNewEvent('USER_FINISHED_DRAGGING', creatures[cCreature], true)
      }
      this._isDragging = false;
      this._isYoinking = false;
      this._isLastYoinked = false;
    }
  }

  updateAgent() {
    if (!roomAdmin || cCreature > 0) {
      this.pushTo(creatures[cCreature], 'creatures', cCreature)
    }
  }

  pushTo(creature, path, to) {
    let upload = {...creature}
    delete upload.compound
    delete upload.parts
    delete upload.partsCom

    if (path === 'suggestions') {
      updateSuggestion(to, upload)
    } else {
      updateCreature(to, upload)
    }

    if (!roomAdmin) {
      sendAgentMessage({meta: meta.CREATURE, [path]: { [to]: upload } }, [KunlaboAction.MESSAGE, KunlaboAction.PERSIST])
    } else {
      if (path === 'suggestions') {
        sendEngineMessage({meta: meta.CREATURE, [path]: { [to]: upload } })
      } else {
        sendEngineMessage({meta: meta.CREATURE, [path]: { [to]: upload } }, [KunlaboAction.MESSAGE, KunlaboAction.PERSIST])
      }
    }
  }

  updateProposal(){
    if (this.hasChanges) {
      this.hasChanges = false
      creatures[cCreature] = new Supercreature(this.constraints, this.roboArm.convert2Genes());
      this.updateAgent()
      saveSingleProposal(creatures[cCreature],'SHAPELAB-UPDATE');
    }
  }
 
  closeShapeLab() {
    this.updateProposal()
  }

  show(){
    noStroke();
    if(this.roboArm != null){
      this.roboArm.show(shapeLab.isEditing, !roomAdmin  && cCreature === 0 && yourTurn || roomAdmin && cCreature > 0)
    }
  }

  initCreature(){
    this.hasChanges = false
    let actualDNA = creatures[cCreature].getDNA();
    let numParts = actualDNA.genes.length;
    this.roboArm = new IKRobot(this.initPos.x, this.initPos.y, this.creatureDimensions);
    for(let i = 0;i<numParts;i++){
      this.roboArm.addPiece(actualDNA.getGenome(i));
    }
  }

  newCreature(){
    this.closeShapeLab()
    creatures.push(new Supercreature(this.constraints));
    cCreature = creatures.length-1
    this.initCreature()
    saveSingleProposal(creatures[cCreature],'NEW-SHAPELAB');
    creatures[cCreature].init();
    this.hasChanges = false
  }

  cloneCreature(){
    this.closeShapeLab()
    creatures.push(Supercreature.fromRemote(creatures[cCreature]));
    cCreature = creatures.length-1
    this.initCreature()
    saveSingleProposal(creatures[cCreature],'CLONE-SHAPELAB');
    creatures[cCreature].init();
    this.hasChanges = true
  }

  addPiece(){
    this.hasChanges = true;
    this.roboArm.addPiece();
  }

  startEditing(){
    this._isEditing = true;
  }
  finishEditing(){
    this._isEditing = false;
    this.updateProposal();
  }

  followMouse(){
    if(this._isEditing){
      this.hasChanges = true
      this.roboArm.updateLast(mouseX, mouseY);
    }
  }

  removePiece(){
    if (this.roboArm.getLength() > 1) { 
      this.hasChanges = true
      this.roboArm.removePiece();
    }
  }

  clearPieces(){
    this.hasChanges = true
    let totalPieces = this.roboArm.getLength()
    for(let i = 0;i<totalPieces-1;i++){
        this.roboArm.removePiece();
    }
  }
}
