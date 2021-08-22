let last

window.addEventListener('load', () => {
    document.getElementById('button').addEventListener('click', () => {
        console.log(KunlaboAction)
        last = Date.now()
        sendAgentMessage({test: "test"})
    })
})

function onEngineMessage (message) {
    console.log((Date.now() - last) / 1000.0)
    console.log(message)
    document.getElementById('message').innerHTML = JSON.stringify(message, null, 4)
}