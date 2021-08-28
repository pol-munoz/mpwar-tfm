
let bestCreature = 0;
let maxFit = 0;
let nFitness = 0;
let gFitness = [];
let generationHistory = []
let proposalsHistory = {};
let gen = 0;
let nextGen = false;
let totalProposals = 3;

class GenerationData {
    constructor(id, normalFit, pieces, w, creatures, bestMember) {
        this.id = id;
        this.normalFit = normalFit;
        this.pieces = pieces;
        this.width = w;
        this.bestCreature = bestMember;
        this.members = [];
        let c;
        for (let i = 0; i < creatures.length; i++) {
            c = new 
            CreatureData(i, creatures[i].getFitness(), gen, creatures[i].getDNA());
            this.members.push(c);
        }
    }
}

class ProposalsData {
    constructor(id, normalFit, pieces, w, creatures) {
        this.id = id;
        this.normalFit = normalFit;
        this.pieces = pieces;
        this.width = w;
        this.members = [];
        let c;
        for (let i = 0; i < creatures.length; i++) {
            c = new 
            (i, creatures[i].getFitness(), gen, creatures[i].getDNA());
            this.members.push(c);
        }
    }
}

class CreatureData
 {
    constructor(nCreature, fit, gen, dna) {
        if (dna != null) {
            this.dna = dna;
            this.numCreature = nCreature;
            this.fit = fit;
            this.gen = gen;
        }
    }
}

class ProposalData
 {
    constructor(id, fit, gen, dna, value) {
        if (dna != null) {
            this.dna = dna;
            this.id = id;
            this.fit = fit;
            this.gen = gen;
            this.value = value;
        }
    }
}

function firstCreatureCreation() {
    gen = 0
    cCreature = 0;
    gFitness = [];
    creatures = []
    maxFit = 0;
    bestCreature = 0;
    console.log("First Generation");
    creatures.push(new Supercreature(dataScenario.constraints));
    creatures[0].defineDNA(new DNA([new Gene(0, roomAdmin ? 'A' : 'U')]));
    creatures[0].init();
}

function createFirstGeneration() {
    gen = 0
    cCreature = 0;
    gFitness = [];
    creatures = []
    maxFit = 0;
    bestCreature = 0;
    console.log("First Generation");
    for (let i = 0; i < totalProposals; i++) {
        creatures.push(new Supercreature(dataScenario.constraints));
        creatures[i].defineDNA();
        saveSingleProposal(creatures[i],'NEW');
        creatures[i].init();
    }
}

function nextGeneration() {    
    let similarityValue = int($('#sliderSimilarity').val())
    let newProposals = int($('#numberProposals').val())
    let lockProposals = $('#cboxChanging').prop("checked")

    generationHistory.push(new GenerationData('E' + dataScenario.name + '-' + gen, nFitness, dataScenario.constraints.minPieces, creatures[bestCreature].w, creatures,
    new CreatureData
    (bestCreature, creatures[bestCreature].fitness, gen, creatures[bestCreature].getDNA())));
    computeNormalFitness();
    creatures = generate(newProposals,similarityValue,lockProposals)
    gen++;
    cCreature = 0;
    maxFit = 0;
    bestCreature = 0;
    nFitness = 0;
    creatures[cCreature].activate();
}

function computeNormalFitness() {
    let sum = 0;
    for (let i = 0; i < creatures.length; i++) {
        if (creatures[i].getFitness() != NaN)
            sum += creatures[i].getFitness();
    }
    nFitness = (sum / creatures.length).toFixed(2);
    gFitness.push(nFitness);
}


function saveProposals() {
    console.log('save proposals');
    console.log(scenario.name)
    saveData(scenario.name);
    var json = {};
    var name = 'test-' + Date.now();
    json.experiment = proposalsHistory;
    saveJSON(json, name);
}

function saveSingleProposal(proposal,value){
    //value can be DELETED // ADDED / MODIFIED 
    let id = proposal.id; 
    let c = new ProposalData(id, proposal.getFitness(), gen, proposal.getDNA(),value);
    if(id in proposalsHistory){
       id = id +'-'+Date.now();
    }
    proposalsHistory[id] = c;
}


function generate(numberProposals,similarityValue,lockUserEdits) {
    let newCreatures = [];
    let matingPool = [];
    let maxFitness = maxFit;

    for (let i = 0; i < creatures.length; i++) {
        let fitnessNormal = map(creatures[i].getFitness(), 0, maxFitness, 0, 1);
        let n = int(fitnessNormal * 100) + 1;
        for (let j = 0; j < n; j++) {
            matingPool.push(creatures[i]);
        }
    }

    for (let i = 0; i < numberProposals; i++) {
        let m = Math.round(random(matingPool.length - 1));
        let d = Math.round(random(matingPool.length - 1));
        let mom = matingPool[m];
        let dad = matingPool[d];
        let momgenes = mom.getDNA();
        let dadgenes = dad.getDNA();
        let childGenes = momgenes.crossover(dadgenes);
        childGenes.mutate();
        newCreatures.push(new Supercreature(dataScenario.constraints));
        newCreatures[i].defineDNA(childGenes);
        saveSingleProposal(newCreatures[i],'NEW');
        newCreatures[i].init();
    }
    return newCreatures;
}

function nextCreature() {
    let currentFit = creatures[cCreature].getFitness();
    if (cCreature > 0) {
        if (maxFit < currentFit) {
            maxFit = currentFit;
            bestCreature = cCreature;
        }
    }
    creatures[cCreature].deactivate();
    cCreature++;
    if (cCreature < creatures.length) {
        creatures[cCreature].activate();
    }
}

function printResultGeneration() {
    background(227);
    let tS = 24;
    push();
    fill(50);
    textSize(tS);
    textAlign(CENTER);
    text('Press G to start next Generation', tS, dataScenario.dims[1] - tS * 6, width, dataScenario.dims[0]);
    pop();
}




