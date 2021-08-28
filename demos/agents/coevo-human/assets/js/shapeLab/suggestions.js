function renderSuggestions() {
    aiSuggestionsView.innerHTML = ''

    if (suggestions.length === 0) {
        aiPlaceholderText.style = ""
    } else {
        aiPlaceholderText.style = "display: none;"
    }

    for (let s in suggestions) {
        if (suggestions.hasOwnProperty(s)) {
            let sugg = render(`
                <div class="Room-suggestion">
                    <div id="sugg-canvas-${s}"></div>
                    <div class="Row" style="justify-content: space-between; padding: 0 15px;">
                    ${ roomAdmin ? `
                        <input class="Button Button-smallest" type="button" id="sugg-go-${s}" value="Shape ${parseInt(s) + 2}">
                    ` : `
                        <input class="Button Button-smallest" type="button" id="sugg-accept-${s}" value="Accept">
                        <input class="Button Button-smallest Button-cancel" type="button" id="sugg-reject-${s}" value="Reject">
                    `}
                    </div>
                </div>
            `)
            aiSuggestionsView.appendChild(sugg)

            renderSuggestion("sugg-canvas-" + s, suggestions[s])

            if (roomAdmin) {
                document.getElementById("sugg-go-" + s).addEventListener('click', () => {
                    displayCreature(parseInt(s) + 1)
                })
            } else {
                document.getElementById("sugg-accept-" + s).addEventListener('click', () => {
                    acceptSuggestion(s)
                })
                document.getElementById("sugg-reject-" + s).addEventListener('click', () => {
                    rejectSuggestion(s)
                })
            }
        }
    }
}

function renderSuggestion(canvas, suggestion) {
    new p5((p) => {
        p.setup = () => {
            p.createCanvas(270, 150)
            p.noLoop()
        }
        p.draw = () => {
            p.scale(0.3, 0.3)
            scenario.renderTo(p)

            suggestion.showTo(p, roomAdmin, 255, true);
        }

    }, canvas)
}