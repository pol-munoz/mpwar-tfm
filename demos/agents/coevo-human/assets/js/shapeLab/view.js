
const shapeLabBrushes = {
    ROTATE: 'rotate',
    MOVE: 'move'
}

const statusPanel = document.getElementById("statusPanel");
const instructionsPanel = document.getElementById("instructionsPanel");
const contextualTools = document.getElementById("contextualTools");
const shapeLabTools = document.getElementById("shapeLabTools");
const adminBar = document.getElementById("adminBar");
const adminTools = document.getElementById("adminTools");
const userBar = document.getElementById("userBar");
const userTools = document.getElementById("userTools");

const spnTitle = document.getElementById("modeTitle");
const spnScenario = document.getElementById("scenarioName");
const spnUserStatus = document.getElementById("userStatus");
const spnUser = document.getElementById("userText");
const spnAdmin = document.getElementById("adminText");

const selEditionMode = document.getElementById("editionMode");
const selVisualizeMode = document.getElementById("visualizeMode");
const btnShapeLabEdit = document.getElementById("shapeLabEdit");
const btnShapeLabNext = document.getElementById("shapeLabNext");
const btnShapeLabPrev = document.getElementById("shapeLabPrev")
const btnShapeLabAdd = document.getElementById("shapeLabAdd");
const btnShapeLabRemove = document.getElementById("shapeLabRemove")
const btnShapeLabNew = document.getElementById("shapeLabNew")
const btnShapeLabClone = document.getElementById("shapeLabClone")
const btnShapeLabClear = document.getElementById("shapeLabClear")

const btnShapeLabPull = document.getElementById("shapeLabPull")
const btnShapeLabPush = document.getElementById("shapeLabPush")
const btnShapeLabAccept = document.getElementById("shapeLabAccept")
const btnShapeLabReject = document.getElementById("shapeLabReject")
const btnShapeLabEndTurn = document.getElementById("shapeLabEndTurn")

const aiChangeCheckbox = document.getElementById("aiChange")
const aiSimilarityRange = document.getElementById("aiSimilarity")

const aiSuggestionsView = document.getElementById("aiSuggestions")
const aiPlaceholderText = document.getElementById("aiPlaceholder")

const selectEditionMode = document.getElementById("editionMode")

/*
const btnSaveProposals = document.getElementById("saveProposals")
const btnBackScenarios = document.getElementById("backToScenarioSelector")
const btnNewGen = document.getElementById("newGen")
const areaGeneration = document.getElementById("areaSimilaritySlider");
const cboxChangeProposals = document.getElementById("cboxChanging");
const cboxAutogenerate = document.getElementById("cboxAutogenerate");
const cboxFastSimulate = document.getElementById("cboxFastSimulate");
*/

btnShapeLabEdit.addEventListener('click', manageEditionMode)
btnShapeLabNext.addEventListener('click', displayNextCreature)
btnShapeLabPrev.addEventListener('click', displayPreviousCreature)
btnShapeLabAdd.addEventListener('click', addNewPiece)
btnShapeLabRemove.addEventListener('click', removeLastPiece)
btnShapeLabNew.addEventListener('click', createNewCreature)
btnShapeLabClone.addEventListener('click', cloneCreature)
btnShapeLabClear.addEventListener('click', clearCurrentCreature)
btnShapeLabPull.addEventListener('click', pullCurrentCreature)
btnShapeLabPush.addEventListener('click', pushCurrentCreature)
btnShapeLabAccept.addEventListener('click', () => acceptSuggestion(0))
btnShapeLabReject.addEventListener('click', () => rejectSuggestion(0))
btnShapeLabEndTurn.addEventListener('click', endCurrentTurn)

selectEditionMode.addEventListener('change', onEditionModeClick)

function onEditionModeClick() {
    logNewEvent('Changed drag mode', this.value)
}

function getShapeLabBrush() { return selectEditionMode.value }

function updateView() {
    scenarioName.innerHTML = dataScenario.name;
    switch (currentState) {
        case controllerStates.SIMULATE:
            showUISimulation();
            break;
        case controllerStates.GENERATE:
            showUINewGen();
            break;
        case controllerStates.SHAPELAB:
            showUIShapeLab();
            break;
    }
}

function setRoomTypeUI() {
    if (roomType == roomTypes.OPTIONS) {
        aiSuggestionsView.parentElement.style = ""
    }

    if (roomAdmin) {
        adminBar.style = ""
        btnShapeLabPull.style = ""
        btnShapeLabPush.style = ""
        aiChangeCheckbox.disabled = true
        aiSimilarityRange.disabled = true

        updateAdminUI()
    } else {
        aiChangeCheckbox.addEventListener('change', updateAiChange)
        aiSimilarityRange.addEventListener('change', updateAiSimilarity)
        switch (roomType) {
            case roomTypes.TURNS:
                userBar.style = ""
                btnShapeLabEndTurn.style = ""
                break
            case roomTypes.OVERLAP:
                userBar.style = ""
                btnShapeLabAccept.style = ""
                btnShapeLabReject.style = ""
                spnUser.innerText = "IA Suggestion"
                break
            case roomTypes.OPTIONS:
                break
        }
        updateUserUI()
    }
}

function updateAIUI(change, similarity) {
    if (change !== undefined) {
        aiChangeCheckbox.checked = change
    }
    if (similarity !== undefined) {
        aiSimilarityRange.value = similarity
    }
}

function updateAdminUI() {
    if (cCreature === 0 || !yourTurn) {
        showAdminUIForPlayerCreature()
        if (cCreature > 0 && roomType === roomTypes.TURNS) {
            btnShapeLabPull.disabled = false
        }
    } else {
        hideAdminUIForPlayerCreature()
    }

    if (roomType === roomTypes.TURNS) {
        spnAdmin.innerHTML = '<b>' + (yourTurn ? 'Your' : 'User\'s') + '</b> turn'
    }
}

function updateUserUI() {
    switch (roomType) {
        case roomTypes.TURNS:
            spnUser.innerHTML = '<b>' + (yourTurn ? 'Your' : 'IA\'s') + '</b> turn' 
            btnShapeLabEndTurn.disabled = !yourTurn
            btnShapeLabEdit.disabled = !yourTurn
            btnShapeLabClear.disabled = !yourTurn
            break
        case roomTypes.OVERLAP:
            if (suggestions.length === 0 || !suggestions[0]) {
                spnUser.innerText = "Generating Suggestion..."
            } else {
                spnUser.innerText = "IA Suggestion"
            }
            btnShapeLabAccept.disabled = suggestions.length === 0 || !suggestions[0]
            btnShapeLabReject.disabled = suggestions.length === 0 || !suggestions[0]
            break
        case roomTypes.OPTIONS:
            break
    }
}

function showAdminUIForPlayerCreature() {
    btnShapeLabPull.disabled = true
    btnShapeLabPush.disabled = true
}
function hideAdminUIForPlayerCreature() {
    btnShapeLabPull.disabled = false
    btnShapeLabPush.disabled = false
}

function showUserStatus(status) {
    spnUserStatus.innerText = status
}

function showSimilaritySlider() {
    if (!cboxChangeProposals.checked) {
        areaGeneration.style = "display: none;";
    } else {
        areaGeneration.style = "";
    }
}

function activateNewGen() {
    let newProposals = int($('#numberProposals').val())
    let maxProposals = int($('#numberProposals').attr('max'))
    let minProposals = int($('#numberProposals').attr('min'))
    if (newProposals > maxProposals) {
        alert("The maximum value of proposals is " + maxProposals)
    } else {
        if (newProposals < minProposals) {
            alert("The minimum value of proposals is " + minProposals)
        } else {
            console.log('New generation');
            autoGen = true;
        }
    }
}

function showUINewGen() {
    spnTitle.innerHTML = 'New Generation';
    var elements = shapeLabTools.querySelectorAll('.genTool');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style = "";
    }
}
function showUISimulation() {
    if (!pauseExperiment) {
        spnTitle.innerHTML = 'Simulation: ON'
    } else {
        spnTitle.innerHTML = 'Simulation: Paused'
    }
    if (showLegend) {
        instructionsPanel.innerHTML = '<div><b>S</b> to enter shapelab mode</div>' +
            '<div><b>F</b> to toggle fast simulation</div>' +
            '<div><b>P</b> to pause/resume the simulation</div>' +
            '<div><b>G</b> to toggle autogenerate</div>'
    }
    var elements = shapeLabTools.querySelectorAll('.editTool');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style = "display: none;";
    }
    var elements = shapeLabTools.querySelectorAll('.genTool');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style = "display: none;";
    }
    var elements = shapeLabTools.querySelectorAll('.simTool');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style = "";
    }
}

function showUIShapeLab() {
    var elements = shapeLabTools.querySelectorAll('.genTool');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style = "display: none;";
    }
    if (!shapeLab.isEditing) {
        hideUIEdition();
    } else {
        showUIEdition();
    }

}
function showUIEdition() {
    btnShapeLabEdit.style = "display: none;";
    selEditionMode.style = "display: none;";
    btnShapeLabClear.style = "display: none;";
    btnShapeLabAdd.style = "";
    btnShapeLabRemove.style = "";

    if (roomAdmin) {
        btnShapeLabNext.style = "display: none;";
        btnShapeLabPrev.style = "display: none;";
        btnShapeLabNew.style = "";
        btnShapeLabClone.style = "";
    }
    spnTitle.innerHTML = 'ShapeLab: Editing'
    if (showLegend) {
        instructionsPanel.innerHTML = '<div><b>S</b> to exit shapelab mode</div>' +
            '<div><b>Enter</b> to confirm edition</div>' +
            '<div><b>N</b> to create a new solution</div>' +
            '<div><b>←/→</b> to move to switch solution</div>' +
            '<div><b>C</b> to clear the current solution</div>' +
            '<div><b>Click / Back</b> to add / remove piece</div>'
    }
}

function hideUIEdition() {
    spnTitle.innerHTML = 'ShapeLab'
    btnShapeLabEdit.style = "";
    selEditionMode.style = "";
    btnShapeLabClear.style = "";
    btnShapeLabAdd.style = "display: none;";
    btnShapeLabRemove.style = "display: none;";

    if (roomAdmin) {
        btnShapeLabNext.style = "";
        btnShapeLabPrev.style = "";
        btnShapeLabNew.style = "";
        btnShapeLabClone.style = "";
    }

    if (showLegend) {
        instructionsPanel.innerHTML = '<div><b>S</b> to exit shapelab mode</div>' +
            '<div><b>E</b> to enter edit mode</div>' +
            '<div><b>N</b> to create a new solution</div>' +
            '<div><b>←/→</b> to move to switch solution</div>' +
            '<div><b>C</b> to clear the current solution</div>' +
            '<div><b>Drag</b> to rotate / move pieces</div>'
    }
}

function updateScenarioDimensions(scale) {
    const containerScenario = document.getElementById("containerScenarioInfo");
    containerScenario.style.width = scale.x + "px";
    containerScenario.style.height = scale.y + "px";
}

/*
//https://codepen.io/seanstopnik/pen/CeLqA
var rangeSliderSimilarity = function () {
    var range = $('#sliderSimilarity'),
        value = $('#labelSliderSimilarity');
    range.on('input', function () {
        let textLabel;
        switch (this.value) {
            case '0': textLabel = 'Highly disimilar'; break;
            case '1': textLabel = 'Disimilar'; break;
            case '2': textLabel = 'Similar'; break;
            case '3': textLabel = 'Highly similar'; break;
        }
        value.html(textLabel);
    });
};
rangeSliderSimilarity();

*/

