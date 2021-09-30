import random
import copy


def on_message(data):
    if data['meta'] != 'CREATURE':
        return {}

    if random.randint(0, 2) > 0:
        return {}

    suggestion = copy.deepcopy(data['creatures'][0])

    n = random.randint(1, 3) + 1

    for i in range(n):
        last = copy.deepcopy(suggestion['dna']['genes'][-1])

        if random.randint(0, 1) > 0:
            last['gene'] += 0.3 * random.random()
        else:
            last['gene'] -= 0.3 * random.random()

        last['creator'] = 'A'
        suggestion['dna']['genes'].append(last)

    index = random.randint(0, 2)

    return {
        'meta': 'CREATURE',
        'suggestions': {
            index: suggestion
        }
    }
