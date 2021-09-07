const roomTypes = {
    TURNS: 'turns',
    OVERLAP: 'overlap',
    OPTIONS: 'options'
}

const meta = {
    STATUS: 'STATUS',
    TURN: 'TURN',
    CREATURE: 'CREATURE',
    AI: 'AI',
    ACCEPT: 'ACCEPT',
    REJECT: 'REJECT'
}

function onPersistLoaded(data) {
    for (let index in data.creatures) {
        if (data.creatures.hasOwnProperty(index)) {
            data.creatures[index].dna.genes = data.creatures[index].dna.genes.slice(0, data.creatures[index].size)
            updateCreature(index, data.creatures[index])
        }
    }
    updateAIUI(data.change, data.similarity)
}

function onEngineMessage(message) {
    switch (message.meta) {
        case meta.TURN:
            yourTurn = true
            logNewEvent('Turn started', creatures[cCreature], true)
            updateUserUI()
            break
        case meta.CREATURE:
            if (message.hasOwnProperty('suggestions')) {
                if (roomType !== roomTypes.TURNS) {
                    for (let index in message.suggestions) {
                        if (message.suggestions.hasOwnProperty(index)) {
                            updateSuggestion(index, message.suggestions[index])
                        }
                    }
                }
            } else {
                for (let index in message.creatures) {
                    if (message.creatures.hasOwnProperty(index)) {
                        updateCreature(index, message.creatures[index])
                    }
                }
            }
            break
    }
}
function onAgentMessage(message) {
    switch (message.meta) {
        case meta.TURN:
            yourTurn = true
            updateAdminUI()
            break
        case meta.CREATURE:
            if (message.hasOwnProperty('suggestions')) {
                for (let index in message.suggestions) {
                    if (message.suggestions.hasOwnProperty(index)) {
                        updateSuggestion(index, message.suggestions[index])
                    }
                }
            } else if (message.hasOwnProperty('creatures')) {
                for (let index in message.creatures) {
                    if (message.creatures.hasOwnProperty(index)) {
                        updateCreature(index, message.creatures[index])
                    }
                }
            }
            break
        case meta.ACCEPT:
            removeSuggestion(message.index)
            break
        case meta.REJECT:
            removeSuggestion(message.index)
            break
        case meta.STATUS:
            showUserStatus(message.status)
            break
            case meta.AI:
                updateAIUI(message.change, message.similarity)
            break
    }
}


window.addEventListener('load', () => {
    if (roomAdmin) {
        // Quick Color Swap because now logic is weird with colors being constants
        let tmp = userColor
        userColor = iaColor
        iaColor = tmp
    } 
    setRoomTypeUI()
})

function updateSuggestion(key, value) {  
    suggestions[key] = Supercreature.fromRemote(value)
    suggestions[key].init()

    logNewEvent('Suggestion received', {channel: key, suggestion: suggestions[key]}, true)
    
    switch (roomType) {
        case roomTypes.OVERLAP:
        case roomTypes.TURNS:
            updateUserUI()
            break
        case roomTypes.OPTIONS:
            renderSuggestions()
            break
    }
}
function removeSuggestion(key) {  
    delete suggestions[key]
    
    switch (roomType) {
        case roomTypes.OVERLAP:
        case roomTypes.TURNS:
            suggestions = []
            updateUserUI()
            break
        case roomTypes.OPTIONS:
            renderSuggestions()
            break
    }
}

function updateCreature(key, value) {
    key = int(key)
    if (cCreature === key) {
        if (currentState === controllerStates.SHAPELAB) {
            shapeLab.closeShapeLab()
        } else {
            if (creatures[cCreature]) {
                creatures[cCreature].deactivate();
            }
        }
    }

    creatures[key] = Supercreature.fromRemote(value);
    creatures[key].init()

    if (cCreature === key) {
        if (currentState === controllerStates.SHAPELAB) {
            shapeLab.initCreature();
        } else {
            if (creatures[cCreature]) {
                creatures[cCreature].activate();
            }
            resetScenario()
        }
    }
}