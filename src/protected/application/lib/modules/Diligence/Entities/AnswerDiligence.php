<?php
namespace Diligence\Entities;

use DateTime;
use Diligence\Controllers\Controller;
use Diligence\Traits\DiligenceSingle;
use Exception;
use MapasCulturais\App;
use MapasCulturais\Entity;
use Doctrine\ORM\Mapping as ORM;
use Diligence\Service\DiligenceInterface;
use Diligence\Entities\Diligence as DiligenceEntity;
use Diligence\Repositories\Diligence as DiligenceRepo;
use Carbon\Carbon;

/**
 * AnswerDiligence 
 * 
 * @ORM\Table(name="answer_diligence")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class AnswerDiligence extends Entity implements DiligenceInterface{
    use DiligenceSingle;

    const STATUS_DRAFT = 0;
    const STATUS_OPEN = 2; // Para respostas salvas não enviadas
    const STATUS_SEND = 3; // Para respostas a diligência enviadas

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="answer_diligence_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

     /**
     * @var \Diligence\Entities\Diligence
     *
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\Diligence", inversedBy="answer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="diligence_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $diligence;

    /**
     * @var \MapasCulturais\Entities\Registration
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $registration;

     /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", nullable=false)
     */
    protected $answer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status = Entity::STATUS_DRAFT;


    /**
     * Salva a resposta do proponente
     *
     * @param [object] $class
     * @return void
     */
    public function createOrUpdate($class): string
    {
        $app = App::i();
        App::i()->applyHook('entity(diligence).createAnswer:before');

        $repo       = new DiligenceRepo();
        //Buscando a ultima diligencia da inscrição passado por parametro
        $lastDiligence = $repo->getIdLastDiligence($class->data['registration']);

        $answer     = new AnswerDiligence();
        $reg        = $app->repo('Registration')->find($class->data['registration']);

        if($class->data['idAnswer'] > 0){
            //Se tiver registro de diligência
            $answerDiligences = App::i()->repo('Diligence\Entities\AnswerDiligence')->find($class->data['idAnswer']);
            $answerDiligences->answer = $class->data['answer'];
            $answerDiligences->createTimestamp = new DateTime();
            $answerDiligences->registration = $reg;
            $answerDiligences->status = $class->data['status'];
         
            $save = self::saveEntity($answerDiligences);
            return json_encode(['message' => 'success', 'entityId' => $save['entityId'], 'status' => 200]);
        }


        $answer->diligence = $lastDiligence;
        $answer->answer = $class->data['answer'];
        $answer->createTimestamp = new DateTime();
        $answer->status = $class->data['status'];
        $answer->registration = $reg;
        $save = self::saveEntity($answer);
       
        App::i()->applyHook('entity(diligence).createAnswer', [&$answer]);

        return json_encode(['message' => 'success', 'entityId' => $save['entityId'], 'status' => 200]);
    }

    public function cancel(Controller $controller): void
    {
        $app =  App::i();
        $answer = $app->repo('\Diligence\Entities\AnswerDiligence')
            ->find($controller->data['idAnswer']);

        try {
            $answer->status = self::STATUS_DRAFT;
            $answer->diligence->situation = Diligence::STATUS_SEND;
            self::saveEntity($answer);
        } catch (Exception $e) {
            $app->error($e->getMessage());
            $controller->json(['message' => 'error', 'status' => 400]);
            return;
        }

        $controller->json(['message' => 'success', 'status' => 200], 200);
    }

    public static function setNumberDaysAnswerDiligence($diligence_receipt_date, $days_to_respond, $type_of_day)
    {
        $date = Carbon::parse($diligence_receipt_date);
        $daysAdds = 0;

        // Consultando no banco os feriados nacionais cadastrados
        $app = App::i();
        $termsHolidays = $app->repo('Term')->findBy(['taxonomy' => 'holiday']);
        $holidays = array_map(function ($term) {
            return $term->term;
        }, $termsHolidays);

        // Loop até que todos os dias sejam adicionados
        while ($daysAdds < $days_to_respond) {
            // Adiciona 1 dia à data atual
            $date->addDay();

            if ($type_of_day === 'Úteis') {
                // Verifica se o dia adicionado é um dia útil (segunda a sexta-feira) e se não é um feriado
                if ($date->isWeekday() && !in_array($date->format('m-d'), $holidays)) $daysAdds++;

                continue;
            }

            $daysAdds++;
        }

        return $date;
    }
}
