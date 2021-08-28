function logNewEvent(type, val = null, isCreature = false) {
    if (!roomAdmin) {
        if (val === null) {
            sendAgentMessage({type}, [KunlaboAction.LOG])
        } else {
            let value = val
            if (isCreature) {
                if (val.hasOwnProperty('suggestion')) {
                    value.suggestion = filterCreature(value.suggestion)
                } else {
                    value = filterCreature(value)
                }
            }
            sendAgentMessage({type, value}, [KunlaboAction.LOG])
        }
    }
}

function filterCreature(creature) {
    let upload = {...creature}
    delete upload.compound
    delete upload.parts
    delete upload.partsCom
    delete upload.x
    delete upload.y
    delete upload.w
    delete upload.h
    delete upload.color
    delete upload.fitness

    upload.dna = upload.dna.genes

    // Must deep copy to avoid modifying logs
    for (let i in upload.dna) {
        if (upload.dna.hasOwnProperty(i)) {
            upload.dna[i] = { ...upload.dna[i] }
        }
    }

    return upload
}