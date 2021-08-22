function onAgentMessage(message) {
    console.log(message)
    document.getElementById('message').innerHTML = JSON.stringify(message, null, 4)
    sendEngineMessage({ received: 'OK' })
}