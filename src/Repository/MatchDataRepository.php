<?php
namespace App\Repository;

use App\Entity\MatchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MatchDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MatchData::class);
    }


    /**
     * @param int $year
     * @param string $type
     * @param string $teamorplayer
     * @param int $limit
     * @return array
     */
    public function SClassement($year, $type, $teamorplayer, $limit): array
    {
        $conn = $this->getEntityManager()->getConnection();

        if ($teamorplayer == 'player') {
            $select = '
			SELECT nr,IF(a.name != "",a.name,"Unnamed") AS name ,IF(a.status = 8,"(DEAD)","") AS dead 
			,IF(a.status = 7,"(SOLD)","") AS sold, b.name AS teams, c.icon,';

            $groupby = 'GROUP BY f_player_id';

            $orderby = 'ORDER BY score DESC,value DESC';
        } else {
            $select = '
			SELECT  b.name ,r.icon,';
            
            $groupby = 'GROUP BY b.team_id';
            
            $orderby = 'ORDER BY score DESC, tv DESC';
        }

        
        switch ($type) {
            case 'bash':
                $select .= 'SUM(bh+si+ki) AS score';
                break;
            
            case 'td':
                $select .= 'SUM(td) AS score';
                break;
            
            case 'xp':
                $select .= 'SUM(cp)+ (SUM(td)*3)+ (SUM(intcpt)*3)+ (SUM(bh+si+ki)*2)+(SUM(mvp)*5) AS score';
                break;
                        
            case 'pass':
                $select .=  'SUM(cp) AS score';
                break;
            
            case 'foul':
                $select .=  'SUM(agg) AS score';
                break;
        }
        
        switch ($type) {
            case 'dead':
                $sql = 'SELECT  b.name, COUNT(*) AS score
				FROM players  a
				JOIN teams b ON a.owned_by_team_id = b.team_id
				WHERE status = 8 AND type = 1 AND b.retired = 0 AND b.year ='.$year.'
				GROUP BY b.name
				HAVING COUNT(*)>0
				ORDER BY score DESC, tv DESC';
                
                if ($limit >0) {
                    $sql .=' LIMIT '.$limit;
                }
                            
                break;
            
            default:
                $sql = $select.'
				FROM match_data
				JOIN players a ON f_player_id = a.player_id
				JOIN teams b ON a.owned_by_team_id = b.team_id	
				JOIN game_data_players c ON a.f_pos_id = c.pos_id
				JOIN races r ON r.race_id = b.f_race_id
				WHERE retired = 0 AND year = '.$year.'
				 '.$groupby.'
				 HAVING score >0
				'.$orderby;
                
                
                if ($limit >0) {
                    $sql .=' LIMIT '.$limit;
                }
                break;
        }


        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (DBALException $e) {
        }

        return [];
    }

    /**
     * @param int $year
     * @return array
     */
    public function totalcas($year): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = 'SELECT SUM(bh+si+ki) AS score
				FROM match_data
				JOIN players a ON f_player_id = a.player_id
							 JOIN teams b ON a.owned_by_team_id = b.team_id	
							 JOIN game_data_players c ON a.f_pos_id = c.pos_id
				WHERE retired = 0 AND year = '.$year.'
				 HAVING score >0
				ORDER BY score DESC, tv DESC';

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (DBALException $e) {
        }

        return [];
    }
}
