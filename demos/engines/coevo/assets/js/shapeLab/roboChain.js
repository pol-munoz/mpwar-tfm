class RoboChain {
    constructor(point, dim, i) {
        this.angle = i;
        if (point.hasOwnProperty("angle")) { // point is probably a Segment
            this.par = point;
            this.a = this.par.b.copy();
            //this.angle = point.angle;          
        } else {
            this.par = false;
            this.a = point;
        }  
        this.b = new p5.Vector();
        this.sw = dim.y;
        this.finishColor;
        this.len = dim.x;
        this.calculateB();
    }

    getAngle(){
        return this.angle;
    }

    setAngle(angle){
        let previousAngle = this.angle
        this.angle = angle;
        this.calculateB();
        return Math.abs(previousAngle - this.angle) < 0.000000001;
    }

    setAngleFromB(x,y){
        this.angle = atan2(y-this.b.y,x-this.b.x)
    }

    setAngleFromA(x,y){
        this.angle = atan2(y-this.a.y,x-this.a.x)
        this.calculateB();
    }

    follow(tx, ty) {
        if (typeof (ty) == "undefined") {
            let targetX = this.child.a.x;
            let targetY = this.child.a.y;
            this.follow(targetX, targetY);
        } else {
            let target = new p5.Vector(tx, ty);
            let dir = p5.Vector.sub(target, this.a);
            this.angle = dir.heading();
            dir.setMag(this.len);
            dir.mult(-1);
            this.a = p5.Vector.add(target, dir);
        }
    }

    setA(pos) {
        this.a = pos.copy();
        this.calculateB();
    }

    calculateB() {
        let dx = this.len * cos(this.angle);
        let dy = this.len * sin(this.angle);
        this.b.set(this.a.x + dx, this.a.y + dy);
    }

    update() {
        this.calculateB();
    }

    updateColor(color) {
        this.finishColor = color;
    }

    show(isEditing, isUser) {
        push();
        let color = isUser ? userColor : iaColor
        if(!isEditing){
            stroke(this.finishColor[0]*color.r, this.finishColor[1]*color.g, this.finishColor[2]*color.b, this.finishColor[3] * 255);
        }else{
            stroke(this.finishColor[0]*color.r, this.finishColor[1]*color.g, this.finishColor[2]*color.b, this.finishColor[3] * 150);
        }
        strokeWeight(this.sw);
        line(this.a.x, this.a.y, this.b.x, this.b.y);
        pop();
    }
}
