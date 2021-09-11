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

    public function readAllByStudyId(Uuid $studyId): array
    {
        if (!$this->elastic->indices()->exists(['index' => self::INDEX])) {
            return [];
        }
        $response = $this->elastic->search(
            [
                'index' => self::INDEX,
                'size' => 100,
                'scroll' => '30s',
                'body' => [
                    'sort' => ['datetime'],
                    'query' => [
                        'term' => [
                            'log.study.keyword' => $studyId->getRaw()
                        ]
                    ]
                ]
            ]
        );

        return $this->performFullQuery($response);
    }


    public function readNewByStudyId(Uuid $studyId): array
    {
        if (!$this->elastic->indices()->exists(['index' => self::INDEX])) {
            return [];
        }
        $response = $this->elastic->search(
            [
                'index' => self::INDEX,
                'size' => 100,
                'scroll' => '30s',
                'body' => [
                    'sort' => ['datetime'],
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'range' => [
                                        'datetime' => [
                                            'gte' => 'now-7d/d',
                                            'lt' => 'now'
                                        ]
                                    ]
                                ],
                                [
                                    'term' => [
                                        'log.study.keyword' => $studyId->getRaw()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $this->performFullQuery($response);
    }

    public function readAllByStudyAndParticipantId(Uuid $studyId, Uuid $participantId): array
    {
        if (!$this->elastic->indices()->exists(['index' => self::INDEX])) {
            return [];
        }
        $response = $this->elastic->search(
            [
                'index' => self::INDEX,
                'size' => 100,
                'scroll' => '30s',
                'body' => [
                    'sort' => ['datetime'],
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

        return $this->performFullQuery($response);
    }

    public function deleteAllByParticipantId(Uuid $participantId): void
    {
        if (!$this->elastic->indices()->exists(['index' => self::INDEX])) {
            return;
        }
        $this->elastic->deleteByQuery(
            [
                'index' => self::INDEX,
                'body' => [
                    'query' => [
                        'term' => [
                            'log.participant.keyword' => $participantId->getRaw()
                        ]
                    ]
                ],
                'wait_for_completion' => false
            ]
        );
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

    private function performFullQuery(array $response): array
    {
        $results = [];
        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
            $results = array_merge($results, $this->transformResults($response));

            $scroll_id = $response['_scroll_id'];

            // Execute a Scroll request and repeat
            $response = $this->elastic->scroll(
                [
                    'body' => [
                        'scroll_id' => $scroll_id,
                        'scroll' => '30s'
                    ]
                ]
            );
        }

        return $results;
    }
}