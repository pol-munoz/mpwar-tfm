<?php

namespace Kunlabo\User\Infrastructure\Framework\Controller;

use Kunlabo\Log\Application\Query\SearchNewLogsByStudy\SearchNewLogsByStudyQuery;
use Kunlabo\Participant\Application\Query\SearchNewParticipantsByStudyId\SearchNewParticipantsByStudyIdQuery;
use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Infrastructure\ChartUtils;
use Kunlabo\Study\Application\Query\SearchStudiesByOwnerId\SearchStudiesByOwnerIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'web_home', methods: ['GET'])]
    public function home(
        QueryBus $queryBus,
        Security $security,
        ChartBuilderInterface $chartBuilder
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();
        $studies = $queryBus->ask(SearchStudiesByOwnerIdQuery::fromOwnerId($owner))->getStudies();

        $studyLookup = [];
        $participants = [];
        $logs = [];

        foreach ($studies as $study) {
            $result = $queryBus->ask(SearchNewParticipantsByStudyIdQuery::fromStudyId($study->getId()))->getParticipants();
            $participants = array_merge($participants, $result);

            $result = $queryBus->ask(SearchNewLogsByStudyQuery::create($study->getId()))->getLogs();
            $logs = array_merge($logs, $result);

            $studyLookup[$study->getId()->getRaw()] = $study;
        }

        usort($participants, function (Participant $a, Participant $b) { return $b->getCreated() > $a->getCreated() ? 1 : - 1; });

        $logsData = $this->prepareLogsData($logs);
        $logsChart = $this->prepareLogsChart($chartBuilder, $logsData);

        return $this->render("app/home.html.twig", [
            'studies' => $studyLookup,
            'participants' => $participants,
            'logCount' => count($logs),
            'logsChart' => $logsChart
        ]);
    }

    private function prepareLogsData(array $logs): array
    {
        $data = [
            'logs' => [
                'labels' => [],
                'data' => [],
            ],
        ];

        if (empty($logs)) {
            return $data;
        }

        $counts = [];

        $start = strtotime('-7 days midnight');
        $now = strtotime('now');

        $time = $start;

        while ($time < $now) {
            $counts[$time] = 0;
            $time += 3600;
        }

        foreach ($logs as $log) {
            $time = intdiv($log->getTimestampSeconds(), 3600) * 3600;
            if ($time >= $start) {
                $counts[$time]++;
            }
        }

        foreach ($counts as $time => $count) {
            $data['logs']['data'][] = [
                't' => $time * 1000,
                'y' => $count
            ];
        }

        return $data;
    }

    private function prepareLogsChart(ChartBuilderInterface $chartBuilder, array $data): Chart
    {
        $logs = $chartBuilder->createChart(Chart::TYPE_BAR);
        $logs->setData(
            [
                'datasets' => [
                    [
                        'label' => 'Logs',
                        'backgroundColor' => 'rgba(38, 79, 146, 0.5)',
                        'hoverBackgroundColor' => 'rgba(38, 79, 146, 0.65)',
                        'barPercentage' => 1.0,
                        'categoryPercentage' => 1.0,
                        'data' => $data['logs']['data']
                    ]
                ]
            ]
        );
        $logs->setOptions(
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
                            'type' => 'time',
                            'time' => [
                                'unit' => 'day'
                            ],
                            'scaleLabel' => ChartUtils::axisLabelConfig('Time (hours)')
                        ]
                    ]
                ],
                'legend' => [
                    'display' => false
                ],
                'title' => ChartUtils::titleConfig('Logs over last week'),
                'aspectRatio' => 1.618
            ]
        );

        return $logs;
    }
}