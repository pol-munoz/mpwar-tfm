const render = html => {
    const wrapper = document.createElement('div')
    wrapper.innerHTML = html
    return wrapper.firstElementChild
}