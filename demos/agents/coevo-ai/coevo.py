import math
import os

def on_agent_message(data):
    print(os.getcwd())
    print(data)
    return { 'response': 'okay', 'test': math.pi }