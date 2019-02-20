<?php
namespace App\Repository;

use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TeamRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Teams::class);
    }


    /**
     * @param int $year
     * @param int $limit
     * @return array
     */
    public function classement($year, $limit): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
SELECT team_id,ra.icon,t.name as "team_name" ,ra.name as "race",co.name,
	
				SUM(IF(team_id = a.team1_id AND a.team1_score>a.team2_score,1,0)+ 
					IF(team_id = a.team2_id AND a.team1_score<a.team2_score,1,0)) AS Win,
					
				SUM(IF(team_id = a.team1_id AND a.team1_score=a.team2_score,1,0)+ 
					IF(team_id = a.team2_id AND a.team1_score=a.team2_score,1,0)) AS Draw,
					
				SUM(IF(team_id = a.team1_id AND a.team1_score<a.team2_score,1,0)+ 
					IF(team_id = a.team2_id AND a.team1_score>a.team2_score,1,0)) AS Lost,(
					
				SUM(IF(team_id = a.team1_id AND a.team1_score>a.team2_score,1,0)+ 
					IF(team_id = a.team2_id AND a.team1_score<a.team2_score,1,0)) * 10 +
				SUM(IF(team_id = a.team1_id AND a.team1_score=a.team2_score,1,0)+ 
					IF(team_id = a.team2_id AND a.team1_score=a.team2_score,1,0)) * 4 +
				SUM(IF(team_id = a.team1_id AND a.team1_score<a.team2_score,1,0)+ 
					IF(team_id = a.team2_id AND a.team1_score>a.team2_score,1,0)) * -5 
					
					)+(
					
                SUM(IF(team_id = a.team1_id AND a.team1_score > 2,1,0)+ 
                    IF(team_id = a.team2_id AND a.team2_score > 2,1,0))  +
                SUM(IF(team_id = a.team1_id AND a.team1_score>a.team2_score AND a.tv2 - a.tv1 >= 250000,1,0)+ 
                    IF(team_id = a.team2_id AND a.team1_score<a.team2_score
                     AND a.tv1 - a.tv2 >= 250000,1,0))  +                    
                SUM(IF(team_id = a.team1_id AND a.team1_score<a.team2_score AND a.team2_score - a.team1_score = 1,1,0)+ 
                    IF(team_id = a.team2_id AND a.team1_score>a.team2_score 
                    AND a.team1_score - a.team2_score = 1,1,0)) +
                    
                SUM(
                    IF( team_id = a.team1_id AND (
     
                    (
						SELECT SUM(md.bh+ md.ki+ md.si+md.agg)
						FROM match_data md
						JOIN players pl ON pl.player_id = md.f_player_id
						JOIN teams te ON te.team_id = pl.owned_by_team_id
						WHERE pl.owned_by_team_id = a.team1_id AND md.f_match_id = a.match_id
                    ) 
					 >4),1,0)+
                                                
                    IF( team_id = a.team2_id AND (
     
                    (
						SELECT SUM(md.bh+ md.ki+ md.si+md.agg)
						FROM match_data md
						JOIN players pl ON pl.player_id = md.f_player_id
						JOIN teams te ON te.team_id = pl.owned_by_team_id
						WHERE pl.owned_by_team_id = a.team2_id AND md.f_match_id = a.match_id
                    ) 
              
					 >4),1,0)
                    
                    
                    ))AS pts,
					
				SUM(IF(team_id = a.team1_id OR team_id = a.team2_id,1,0)) AS nbrg,

				FLOOR(tv/1000) AS tv

				FROM teams t

				JOIN matches a ON a.team1_id = team_id OR a.team2_id = team_id
                JOIN coaches co ON co.coach_id = owned_by_coach_id
				JOIN races ra ON ra.race_id = t.f_race_id
				WHERE retired = 0 AND year = '.$year.'

				GROUP BY t.name
				ORDER BY pts DESC,nbrg DESC,tv DESC';
                
                
        if ($limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }


        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (DBALException $e) {
        }
        return [];
    }
}
