<?php

namespace Kunlabo\Log\Infrastructure\Persistence;

use Elasticsearch\Client;
use Kunlabo\Log\Domain\Log;
use Kunlabo\Log\Domain\LogRepository;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class ElasticLogRepository implements LogRepository
{
    private const INDEX = 'kunlabo-logs';

    public function __construct(private Client $elastic)
    {
    }

    // TODO paginate or sth (get all besides the 10 which is default size)

    public function readAllByStudyId(Uuid $studyId): array
    {
        $results = $this->elastic->search(
            [
                'index' => self::INDEX,
                'body' => [
                    'query' => [
                        'term' => [
                            'log.study.keyword' => $studyId->getRaw()
                        ]
                    ]
                ]
            ]
        );

        return $this->transformResults($results);
    }

    public function readAllByStudyAndParticipantId(Uuid $studyId, Uuid $participantId): array
    {
        $results = $this->elastic->search(
            [
                'index' => self::INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'log.study.keyword' => $studyId->getRaw()
                                    ]
                                ],
                                [
                                    'term' => [
                                        'log.participant.keyword' => $participantId->getRaw()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $this->transformResults($results);
    }

    private function transformResults(array $results): array
    {
        return array_map(
            function ($result) {
                return Log::create(
                    $result['_source']['datetime'],
                    $result['_source']['log']['study'],
                    $result['_source']['log']['participant'],
                    $result['_source']['log']['body'],
                );
            },
            $results['hits']['hits']
        );
    }
}