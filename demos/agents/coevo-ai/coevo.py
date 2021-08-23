import math

def on_agent_message(data):
    print('Hey')
    print(data)
    return { 'response': 'okay', 'test': math.pi }