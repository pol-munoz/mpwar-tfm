import os
import importlib.util
import json
import requests
from sseclient import SSEClient
from multiprocessing import Process


def handle_message(data):
    url = 'http://nginx/ai/' + data['extras']['study'] + '/' + data['extras']['participant']
    path = './kunlabo/agents/' + data['extras']['agent'] + data['extras']['file']
    file = path.split('/')[-1]
    folder = path[:-len(file)]
    body = data['body']

    if file.endswith('.py'):
        file = file[:-2]

    importlib.invalidate_caches()
    spec = importlib.util.spec_from_file_location(file, path)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)

    os.chdir(folder)
    result = module.on_agent_message(body)

    if result:
        post = {
            'actions': ['MESSAGE'],
            'body': result
        }
        requests.post(url, json=post)


updates = SSEClient(
    os.environ['HUB_URL'],
    params={'topic': ['http://kunlabo.com/agent/{study}/{participant}']},
)

for update in updates:
    if update.data:
        data = json.loads(update.data)
        if 'extras' in data:
            process = Process(target=handle_message, args=(data,))
            process.start()
