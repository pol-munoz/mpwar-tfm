<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use Kunlabo\Log\Application\Query\SearchLogsByStudyAndParticipant\SearchLogsByStudyAndParticipantQuery;
use Kunlabo\Participant\Application\Query\SearchParticipantsByStudyId\SearchParticipantsByStudyIdQuery;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Infrastructure\ChartUtils;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class StudyInsightsController extends AbstractController
{
    #[Route('/insights/{id}', name: 'web_studies_insights', methods: ['GET'])]
    public function studyInsights(
        QueryBus $queryBus,
        Security $security,
        ChartBuilderInterface $chartBuilder,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

        $owner = $security->getUser()->getId();
        if ($study === null || !$study->isOwnedBy($owner)) {
            throw $this->createNotFoundException();
        }

        $participants = $queryBus->ask(SearchParticipantsByStudyIdQuery::create($id))->getParticipants();

        $participantData = $this->prepareParticipantData($participants);

        $ageChart = $this->prepareAgeChart($chartBuilder, $participantData);
        $genderChart = $this->prepareGenderChart($chartBuilder, $participantData);
        $handChart = $this->prepareHandChart($chartBuilder, $participantData);

        $logs = [];
        $count = 0;

        foreach ($participants as $participant) {
            $results = $queryBus->ask(
                SearchLogsByStudyAndParticipantQuery::create($id, $participant->getId()->getRaw())
            )->getLogs();
            $logs[$participant->getId()->getRaw()]['logs'] = $results;
            $logs[$participant->getId()->getRaw()]['name'] = $participant->getName()->getRaw();

            $count += count($results);
        }

        $logsData = $this->prepareLogsData($logs);
        $typeChart = $this->prepareTypeChart($chartBuilder, $logsData);
        $journeyTimeChart = $this->prepareJourneyTimeChart($chartBuilder, $logsData);
        $journeyActionChart = $this->prepareJourneyActionChart($chartBuilder, $logsData);

        return $this->render(
            'app/studies/insights.html.twig',
            [
                'study' => $study,
                'ageChart' => $ageChart,
                'genderChart' => $genderChart,
                'handChart' => $handChart,
                'participantCount' => count($participants),
                'logCount' => $count,
                'typeCount' => $logsData['typeCount'],
                'typeChart' => $typeChart,
                'journeyTimeChart' => $journeyTimeChart,
                'journeyActionChart' => $journeyActionChart
            ]
        );
    }

    private function prepareParticipantData($participants): array
    {
        $genderLabels = ['Male', 'Female', 'Non-binary'];
        $handLabels = ['Left', 'Right', 'Ambidextrous'];

        if (empty($participants)) {
            return [
                'ages' => [
                    'labels' => [0],
                    'data' => [0]
                ],
                'genders' => [
                    'labels' => $genderLabels,
                    'data' => [0, 0, 0]
                ],
                'hands' => [
                    'labels' => $handLabels,
                    'data' => [0, 0, 0]
                ]
            ];
        }

        $data = [
            'ages' => [],
            'genders' => [
                'labels' => $genderLabels,
                'data' => [0, 0, 0]
            ],
            'hands' => [
                'labels' => $handLabels,
                'data' => [0, 0, 0]
            ]
        ];

        $minAge = PHP_INT_MAX;
        $maxAge = PHP_INT_MIN;

        foreach ($participants as $participant) {
            $age = $participant->getAge()->getRaw();

            if ($age < $minAge) {
                $minAge = $age;
            }
            if ($age > $maxAge) {
                $maxAge = $age;
            }
        }

        $data['ages']['labels'] = range($minAge, $maxAge);
        $data['ages']['data'] = array_fill(0, $maxAge - $minAge + 1, 0);

        foreach ($participants as $participant) {
            $age = $participant->getAge()->getRaw();
            $data['ages']['data'][$age - $minAge]++;

            if ($participant->getGender()->isMale()) {
                $data['genders']['data'][0]++;
            } else {
                if ($participant->getGender()->isFemale()) {
                    $data['genders']['data'][1]++;
                } else {
                    if ($participant->getGender()->isNonBinary()) {
                        $data['genders']['data'][2]++;
                    }
                }
            }

            if ($participant->getHandedness()->isLeft()) {
                $data['hands']['data'][0]++;
            } else {
                if ($participant->getHandedness()->isRight()) {
                    $data['hands']['data'][1]++;
                } else {
                    if ($participant->getHandedness()->isAmbidextrous()) {
                        $data['hands']['data'][2]++;
                    }
                }
            }
        }

        return $data;
    }

    private function prepareLogsData(array $logs): array
    {
        $data = [
            'typeCount' => 0,
            'colors' => [],
            'types' => [
                'labels' => [],
                'data' => [],
            ],
            'journeys' => [
                'time' => [
                    'labels' => [],
                    'datasets' => []
                ],
                'action' => [
                    'labels' => [],
                    'datasets' => []
                ]
            ]
        ];

        if (empty($logs)) {
            return $data;
        }

        $lookup = [];
        $types = 0;

        foreach ($logs as $participant) {
            $minTimestamp = PHP_INT_MAX;
            foreach ($participant['logs'] as $log) {
                if ($log->hasType()) {
                    $type = $log->getType();

                    if (!array_key_exists($type, $lookup)) {
                        $color = ChartUtils::uniqueAlphaColor($types, 0.7);

                        $lookup[$type] = $types;
                        $data['types']['labels'][] = $type;
                        $data['types']['data'][] = 0;
                        $data['colors'][] = $color;

                        $data['journeys']['time']['datasets'][] = [
                            'data' => [],
                            'pointStyle' => ChartUtils::POINT_STYLES[$types % ChartUtils::POINT_STYLES_NUMBER],
                            'pointBackgroundColor' => $color,
                            'pointBorderColor' => $color,
                            'label' => $type,
                            'backgroundColor' => $color,
                        ];
                        $data['journeys']['action']['datasets'][] = [
                            'data' => [],
                            'pointStyle' => ChartUtils::POINT_STYLES[$types % ChartUtils::POINT_STYLES_NUMBER],
                            'pointBackgroundColor' => $color,
                            'pointBorderColor' => $color,
                            'label' => $type,
                            'backgroundColor' => $color,
                        ];

                        $types++;
                    }
                    $data['types']['data'][$lookup[$type]]++;

                    if ($log->getTimestamp() < $minTimestamp) {
                        $minTimestamp = $log->getTimestamp();
                    }
                }
            }

            $data['journeys']['time']['labels'][] = $participant['name'];
            $data['journeys']['action']['labels'][] = $participant['name'];

            foreach ($participant['logs'] as $x => $log) {
                if ($log->hasType()) {
                    $type = $log->getType();

                    $data['journeys']['time']['datasets'][$lookup[$type]]['data'][] = [
                        'x' => ($log->getTimestamp() - $minTimestamp) / 1000.0,
                        'y' => $participant['name']
                    ];
                    $data['journeys']['action']['datasets'][$lookup[$type]]['data'][] = [
                        'x' => $x,
                        'y' => $participant['name']
                    ];
                }
            }
        }

        $data['typeCount'] = $types;
        return $data;
    }

    private function prepareAgeChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $ages = $chartBuilder->createChart(Chart::TYPE_BAR);
        $ages->setData(
            [
                'labels' => $data['ages']['labels'],
                'datasets' => [
                    [
                        'label' => 'Participants',
                        'backgroundColor' => 'rgba(38, 79, 146, 0.5)',
                        'hoverBackgroundColor' => 'rgba(38, 79, 146, 0.65)',
                        'barPercentage' => 1.0,
                        'categoryPercentage' => 1.0,
                        'data' => $data['ages']['data']
                    ]
                ]
            ]
        );
        $ages->setOptions(
            [
                'scales' => [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true,
                                'precision' => 0
                            ],
                            'scaleLabel' => ChartUtils::axisLabelConfig('# of Participants')
                        ]
                    ],
                    'xAxes' => [
                        [
                            'scaleLabel' => ChartUtils::axisLabelConfig('Age (years)')
                        ]
                    ]
                ],
                'legend' => [
                    'display' => false
                ],
                'title' => ChartUtils::titleConfig('Age distribution'),
                'aspectRatio' => 1.618
            ]
        );

        return $ages;
    }

    private function prepareGenderChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $genders = $chartBuilder->createChart(Chart::TYPE_PIE);
        $genders->setData(
            [
                'labels' => $data['genders']['labels'],
                'datasets' => [
                    [
                        'data' => $data['genders']['data'],
                        'backgroundColor' => [
                            'rgb(27, 144, 154)',
                            'rgb(235, 71, 41)',
                            'rgb(240, 173, 47)'
                        ]
                    ]
                ]
            ]
        );
        $genders->setOptions(
            [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                    ]
                ],
                'title' => ChartUtils::titleConfig('Gender distribution'),
                'aspectRatio' => 1.618
            ]
        );

        return $genders;
    }

    private function prepareHandChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $hands = $chartBuilder->createChart(Chart::TYPE_PIE);
        $hands->setData(
            [
                'labels' => $data['hands']['labels'],
                'datasets' => [
                    [
                        'data' => $data['hands']['data'],
                        'backgroundColor' => [
                            'rgb(47, 173, 240)',
                            'rgb(235, 41, 71)',
                            'rgb(154, 27, 144)',
                        ]
                    ]
                ]
            ]
        );
        $hands->setOptions(
            [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                    ]
                ],
                'title' => ChartUtils::titleConfig('Handedness distribution'),
                'aspectRatio' => 1.618
            ]
        );

        return $hands;
    }

    private function prepareTypeChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $types = $chartBuilder->createChart(Chart::TYPE_BAR);
        $types->setData(
            [
                'labels' => $data['types']['labels'],
                'datasets' => [
                    [
                        'label' => 'Logs',
                        'backgroundColor' => $data['colors'],
                        'barPercentage' => 1.0,
                        'categoryPercentage' => 1.0,
                        'data' => $data['types']['data']
                    ]
                ]
            ]
        );
        $types->setOptions(
            [
                'scales' => [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true,
                                'precision' => 0
                            ],
                            'scaleLabel' => ChartUtils::axisLabelConfig('# of Logs')
                        ]
                    ],
                    'xAxes' => [
                        [
                            'scaleLabel' => ChartUtils::axisLabelConfig('Log type')
                        ]
                    ]
                ],
                'legend' => [
                    'display' => false
                ],
                'title' => ChartUtils::titleConfig('Log type frequency'),
                'aspectRatio' => 1.618
            ]
        );

        return $types;
    }

    private function prepareJourneyTimeChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $types = $chartBuilder->createChart(Chart::TYPE_SCATTER);
        $types->setData(
            [
                'datasets' => $data['journeys']['time']['datasets']
            ]
        );

        $types->setOptions(
            [
                'scales' => [
                    'xAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true,
                                'precision' => 0
                            ],
                            'scaleLabel' => ChartUtils::axisLabelConfig('Time (seconds)')
                        ]
                    ],
                    'yAxes' => [
                        [
                            'type' => 'category',
                            'labels' => $data['journeys']['time']['labels'],
                            'scaleLabel' => ChartUtils::axisLabelConfig('Participant')
                        ]
                    ]
                ],
                'elements' => [
                    'point' => [
                        'radius' => 8,
                        'hoverRadius' => 10
                    ]
                ],
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ]
                ],
                'title' => ChartUtils::titleConfig('Interaction journey over time'),
                'maintainAspectRatio' => false
            ]
        );

        return $types;
    }

    private function prepareJourneyActionChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $types = $chartBuilder->createChart(Chart::TYPE_SCATTER);
        $types->setData(
            [
                'datasets' => $data['journeys']['action']['datasets']
            ]
        );

        $types->setOptions(
            [
                'scales' => [
                    'xAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true,
                                'precision' => 0
                            ],
                            'scaleLabel' => ChartUtils::axisLabelConfig('Action #')
                        ]
                    ],
                    'yAxes' => [
                        [
                            'type' => 'category',
                            'labels' => $data['journeys']['action']['labels'],
                            'scaleLabel' => ChartUtils::axisLabelConfig('Participant')
                        ]
                    ]
                ],
                'elements' => [
                    'point' => [
                        'radius' => 8,
                        'hoverRadius' => 10
                    ]
                ],
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ]
                ],
                'title' => ChartUtils::titleConfig('Interaction journey over actions'),
                'maintainAspectRatio' => false
            ]
        );

        return $types;
    }
}