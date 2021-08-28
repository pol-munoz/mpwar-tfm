const ALTER_MUTATION_RATE = 0.1;
const GROWTH_MUTATION_RATE = 0.1;

const VARIABLE_WIDTH = false;
class Gene{
  constructor(gene,creator){
    if(gene){
      this.gene = gene;
      this.creator = creator;
    }else{
      this.gene = random(0,2*Math.PI);
      this.creator = 'M' //H-Hum M-Machine B-Both
    }
  }
}

class DNA{
    constructor(genes){
      this.mutationRate = ALTER_MUTATION_RATE; 
      this.mutationGrowthRate = GROWTH_MUTATION_RATE ; 
      if(genes){
        this.genes = genes;
      }else{
        this.genes = [];
        for(let i = 0;i<dataScenario.constraints.minPieces ; i++){
          this.genes.push(new Gene());
        }
      }
    }

    static fromRemote(data) {
      let genes = []

      for (let gene of data.genes) {
        genes.push(new Gene(gene.gene, gene.creator))
      }
      return new DNA(genes)
    }

    toString(){
      let dnaString = "";
      for(let i = 0;i<this.genes.length ; i++){
        dnaString = this.genes[i].gene + " ";
      }
      return dnaString;
    }
    getGenome(i){ return this.genes[i]; }
  
    setGenome(i, angle){ this.genes[i].gene = angle;
    }
  
    getGenes(){
      return this.genes;
    }
    crossover(dna){
      let child = new Array(this.genes.length);
      let crossover = int(random(dna.length));
      for (let i = 0; i < this.genes.length; i++) {
        if (i > crossover) child[i] = this.genes[i];
        else child[i] = dna.genes[i];
      }
      let newgenes = new DNA(child);
      return newgenes;
    }
    mutate(){
      for(let i = 0 ;i<this.genes.length ; i++){
        if(random(1) < this.mutationRate){
          this.genes[i] = new Gene();
        }
      }
      if(this.mutationGrowthRate > 0){ 
        while(random(1)<this.mutationGrowthRate ){ 
          if(this.genes.length > dataScenario.constraints.minPieces) this.genes.pop(this.genes.length);
        }
      }
      if(this.mutationGrowthRate > 0){
        while(random(1)<this.mutationGrowthRate ){ 
          this.genes.push(new Gene());
        }
      }
    }
  }
  