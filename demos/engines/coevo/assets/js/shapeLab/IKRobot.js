class IKRobot {
    constructor(x, y ,dimensions) {
        this.base = new p5.Vector(x, y);
        this.dimensions = dimensions;
        this.target = new p5.Vector(0, 0);
        this.joints = [];
        this.angles = [];
    }

    addPiece(gene) {
        let creator = roomAdmin ? 'A' : 'U'
        let angle;
        if(gene){
            creator = gene.creator;
            angle = gene.gene
        }
        if(this.joints.length == 0){
            this.joints.push(new RoboChain(this.base, this.dimensions,angle));
            this.angles.push(new Gene(angle,creator));
        }else{
            let nJoin = this.joints[this.joints.length - 1]
            if(gene === undefined){
                angle = nJoin.angle
            }
            //nJoin.angle = angle
            this.joints.push(new RoboChain(nJoin, this.dimensions, angle));
            this.angles.push(new Gene(angle,creator));
            this.joints[this.joints.length - 2].child = this.joints[this.joints.length - 1];
        }
        this.defineColor();
    }

    removePiece() {
        this.joints.pop();
        this.angles.pop();
        this.defineColor();
    }

    update(x, y) {
        let end = this.joints[this.joints.length- 1];
        this.target.set(x, y);
        end.follow(x, y);
        end.update();
        for (let i = this.joints.length  - 1; i >= 0; i--) {
            this.joints[i].follow(this.joints[i + 1]);
            this.joints[i].update();
        }
        this.joints[0].setA(this.base);
        for (let i = 1; i < this.joints.length ; i++) {
            this.joints[i].setA(this.joints[i - 1].b);
        }
    }

    updateLast(x, y) {
        let end;
        end = this.joints[this.joints.length - 1];
        end.setAngleFromA(x,y)
        let creator = roomAdmin ? 'A' : 'U';
        this.angles[this.angles.length-1] = new Gene(end.getAngle(),creator);
        this.defineColor();
    }

    show(isEditing, isUser) {
        for (let i = 0; i < this.joints.length; i++) {
            this.joints[i].show(isEditing, isUser);
        }
    }

    defineColor() {
        let min = 0.45
        let max = 0.9
        let step = (max - min) / this.joints.length

        for (let i = 0; i < this.joints.length; i++) {
            let cur = min + step * i
            let color = [cur, cur, cur, max - step * i];
            this.joints[i].updateColor(color);
        }

    }

    defineColorByCreator() {
        let color;
        for (let i = 0; i < this.joints.length; i++) {
            if(this.angles[i].creator == 'M'){
                color = [20, 20, 20, 200];
            }else{
                color = [50, 200, 200, 200];
            }
            this.joints[i].updateColor(color);
        }

    }

    reorient(piece) {
        this.reorientation = !this.reorientation;
        this.pieceReo = piece;
    }

    getClosest(x,y) {
        let iMin = -1;
        let min = Infinity;
        let destination = createVector(x,y)

        for (let i = 0; i < this.joints.length; i++) {
            let d = destination.dist(this.joints[i].a)
            if (d < min) {
                min = d;
                iMin = i;
            }
        }

        return iMin;
    }

    getDistanceFromJoint(i, x, y) {
        let destination = createVector(x,y)
        return destination.dist(this.joints[i].a)
    }

    isClosestToEnd(x, y, radius) {
        let destination = createVector(x,y)
        let dLast = destination.dist(this.joints[this.joints.length - 1].a)
        let dEnd = destination.dist(this.joints[this.joints.length - 1].b)
        return dLast > dEnd && dEnd <= radius
    }

    //We need to fix update what user has touched. 
     yoink(i, x, y, isLast, propagate=true) {
        let xStack = []
        let yStack = []

        let att = propagate ? 0.1 : 0.0;
        let iAtt = 1.0 - att;
   
        if (isLast) {
            xStack.push(this.joints[this.joints.length - 1].a.x * iAtt + x * att)
            yStack.push(this.joints[this.joints.length - 1].a.y * iAtt + y * att)
        } else {
            xStack.push(x)
            yStack.push(y)
        }

        for (let j = i - 1; j > 0; j--) {
            xStack.push(this.joints[j].a.x * iAtt + x * att)
            yStack.push(this.joints[j].a.y * iAtt + y * att)
        }

        for (let j = 1; j <= i; j++) {
            let destination = createVector(xStack.pop(),yStack.pop())
            this.joints[j].a = this.joints[j - 1].b
            
            let origin = this.joints[j-1].a
            let angle = atan2(destination.y-origin.y,destination.x-origin.x)

            let sameAngle = this.joints[j-1].setAngle(angle)
            let creator = this.angles[j-1].creator;
            if(!sameAngle){
                creator = 'U';
            }
            this.angles[j-1] = new Gene(this.joints[j-1].getAngle(),creator);
     
        }

        for (let j = i + 1; j < this.joints.length; j++) {
            this.joints[j-1].calculateB()
            this.joints[j].a = this.joints[j - 1].b.copy()
        }
        this.joints[this.joints.length-1].calculateB()
        this.defineColor();
    }

    getLastPos() {
        return this.joints[this.joints.length - 1].b;
    }

    getAngleFromLast(x,y) {
        let destination = createVector(x,y)
        let origin = this.joints[this.joints.length  - 1].b
        let angle = atan2(destination.y-origin.y,destination.x-origin.x)

        return angle;
    }

    convert2Genes() {
        let genes = []
        for (let i = 0; i < this.angles.length; i++) {
            genes.push(this.angles[i]);
        }
        let dna = new DNA(genes);
        return dna;
    }

    getLength() {
        return this.angles.length
    }
}
