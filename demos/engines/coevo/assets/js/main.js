let last

window.addEventListener('load', () => {
    document.getElementById('button').addEventListener('click', () => {
        last = Date.now()
        sendAgentMessage({test: "test"}, [KunlaboAction.MESSAGE, KunlaboAction.LOG])
    })
})

function onEngineMessage (message) {
    console.log((Date.now() - last) / 1000.0)
    console.log(message)
    document.getElementById('message').innerHTML = JSON.stringify(message, null, 4)
}