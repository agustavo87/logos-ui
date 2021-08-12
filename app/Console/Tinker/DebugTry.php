<?php

declare(strict_types=1);

namespace App\Console\Tinker;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class DebugTry
{
    public function run()
    {
        return 'Hola Carola!';
    }

    public function makeParticipationsMinRelevance()
    {
        $query = DB::table('participations')
            ->select(
                'source_id',
                DB::raw('MIN(relevance) AS min_relevance')
            )->groupBy('source_id');
        return $query;
    }

    public function firstParticipations()
    {
        $participationsMinRelevances = $this->makeParticipationsMinRelevance();
        $query = DB::table('participations')
          ->joinSub(
              $participationsMinRelevances,
              'min_relevances',
                function (JoinClause $join) {
                    $join->on('participations.source_id', '=', 'min_relevances.source_id');
                }
          );
          $query->select('participations.*', 'min_relevances.min_relevance');

          $query->whereRaw('participations.relevance = min_relevances.min_relevance');

          return $query;
    }
}
