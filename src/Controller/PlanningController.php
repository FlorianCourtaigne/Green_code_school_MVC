<?php

namespace App\Controller;

use App\Model\PlanningManager;

class PlanningController extends AbstractController
{
    /**
     * Show plannings
     */
    public function showPlannings(): ?array
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            return null;
        }

        $thisWeek = date('W');
        $planningManager = new PlanningManager();
        $plannings = $planningManager->selectPlannings((int)$thisWeek, 'p.week');
        $weeks = [];
        foreach ($plannings as $planning) {
            $weeks[] = $this->getStartAndEndDate($planning['week'], $planning['promo_name']);
        }
        return $weeks;
    }

    public function getStartAndEndDate($week, $promo)
    {
        $ret = [];
        $dto = new \DateTime();
        $year = (int) date('Y');
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+4 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        $ret['promo_name'] = $promo;
        return $ret;
    }
}
