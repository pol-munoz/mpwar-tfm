<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use Kunlabo\Log\Application\Query\SearchLogsByStudyAndParticipant\SearchLogsByStudyAndParticipantQuery;
use Kunlabo\Participant\Application\Query\SearchParticipantsByStudyId\SearchParticipantsByStudyIdQuery;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class StudyInsightsController extends AbstractController
{
    #[Route('/insights/{id}', name: 'web_studies_insights', methods: ['GET'])]
    public function studyInsights(
        QueryBus $queryBus,
        ChartBuilderInterface $chartBuilder,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();
        if ($study === null) {
            throw $this->createNotFoundException();
        }

        $participants = $queryBus->ask(SearchParticipantsByStudyIdQuery::create($id))->getParticipants();

        $participantData = $this->prepareParticipantData($participants);

        $ages = $this->prepareAges($chartBuilder, $participantData);
        $genders = $this->prepareGenders($chartBuilder, $participantData);
        $hands = $this->prepareHands($chartBuilder, $participantData);

        $logs = [];
        foreach ($participants as $participant) {
            $results = $queryBus->ask(SearchLogsByStudyAndParticipantQuery::create($id, $participant->getId()->getRaw()))->getLogs();
            $logs = array_merge($logs, $results);
        }

        $logsData = $this->prepareLogsData($logs);
        $types = $this->prepareTypes($chartBuilder, $logsData);

        return $this->render(
            'app/studies/insights.html.twig',
            [
                'study' => $study,
                'ages' => $ages,
                'genders' => $genders,
                'hands' => $hands,
                'participants' => count($participants),
                'logs' => count($logs),
                'hasTypes' => $logsData['hasTypes'],
                'types' => $types,
            ]
        );
    }

    private function titleConfig(string $text): array
    {
        return [
            'display' => true,
            'text' => $text,
            'fontFamily' => "'Poppins', sans-serif",
            'fontSize' => 18,
            'fontColor' => '#040910',
            'padding' => 20
        ];
    }

    private function axisLabelConfig(string $text): array
    {
        return [
            'display' => true,
            'labelString' => $text,
            'fontSize' => 14,
            'fontColor' => '#1d3d70',
            'fontStyle' => 'bold',
            'padding' => 10
        ];
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
            'hasTypes' => false,
            'types' => [
                'labels' => [],
                'data' => [],
                'colors' => []
            ]
        ];

        if (empty($logs)) {
            return $data;
        }

        $lookup = [];
        $types = 0;

        Utils::startColors(25);
        foreach ($logs as $log) {
            if ($log->hasType()) {
                $data['hasTypes'] = true;

                $type = $log->getType();
                if (array_key_exists($type, $lookup)) {
                    $data['types']['data'][$lookup[$type]]++;
                } else {
                    $lookup[$type] = $types;
                    $data['types']['labels'][] = $type;
                    $data['types']['colors'][] = Utils::randomAlphaColor(0.6);
                    $data['types']['data'][] = 1;
                    $types++;
                }
            }

        }
        Utils::endColors();

        return $data;
    }

    private function prepareAges(ChartBuilderInterface $chartBuilder, array $data): Chart
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
                            'scaleLabel' => $this->axisLabelConfig('# of Participants')
                        ]
                    ],
                    'xAxes' => [
                        [
                            'barPercentage' => 1.0,
                            'categoryPercentage' => 1.0,
                            'scaleLabel' => $this->axisLabelConfig('Age (years)')
                        ]
                    ]
                ],
                'legend' => [
                    'display' => false
                ],
                'title' => $this->titleConfig('Age distribution'),
                'aspectRatio' => 1.5
            ]
        );

        return $ages;
    }

    private function prepareGenders(ChartBuilderInterface $chartBuilder, array $data): Chart
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
                'title' => $this->titleConfig('Gender distribution'),
                'aspectRatio' => 1.5
            ]
        );

        return $genders;
    }

    private function prepareHands(ChartBuilderInterface $chartBuilder, array $data): Chart
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
                'title' => $this->titleConfig('Handedness distribution'),
                'aspectRatio' => 1.5
            ]
        );

        return $hands;
    }

    private function prepareTypes(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $types = $chartBuilder->createChart(Chart::TYPE_BAR);
        $types->setData(
            [
                'labels' => $data['types']['labels'],
                'datasets' => [
                    [
                        'label' => 'Logs',
                        'backgroundColor' => $data['types']['colors'],
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
                            'scaleLabel' => $this->axisLabelConfig('# of Logs')
                        ]
                    ],
                    'xAxes' => [
                        [
                            'barPercentage' => 1.0,
                            'categoryPercentage' => 1.0,
                            'scaleLabel' => $this->axisLabelConfig('Log type')
                        ]
                    ]
                ],
                'legend' => [
                    'display' => false
                ],
                'title' => $this->titleConfig('Log type frequency'),
                'aspectRatio' => 1.5
            ]
        );

        return $types;
    }
}