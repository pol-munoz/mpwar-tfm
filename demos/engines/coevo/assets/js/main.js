window.addEventListener('load', () => {
    sendAgentMessage({test: "test"})
})

function onEngineMessage (message) {
    console.log(message)
    document.getElementById('message').innerHTML = JSON.stringify(message, null, 4)
}