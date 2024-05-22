<?php
namespace Diligence\Entities;

use DateTime;
use \MapasCulturais\App;
use MapasCulturais\Entity;
use Doctrine\ORM\Mapping as ORM;
use Respect\Validation\Rules\Json;
use Diligence\Controllers\Controller;
use Diligence\Service\DiligenceInterface;
use Diligence\Repositories\Diligence as DiligenceRepo;
use Carbon\Carbon;

/**
 * AnswerDiligence 
 * 
 * @ORM\Table(name="answer_diligence")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class AnswerDiligence extends \MapasCulturais\Entity implements DiligenceInterface{
    use \Diligence\Traits\DiligenceSingle;

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
    public function create($class): string
    {
        $app = App::i();
        App::i()->applyHook('entity(diligence).createAnswer:before');

        $repo       = new DiligenceRepo();
        // $answerDiligences = $repo->findBy('Diligence\Entities\AnswerDiligence', ['diligence' => $diligence]);

        //Buscando a ultima diligencia da inscrição passado por parametro
        $lastDiligence = $repo->getIdLastDiligence($class->data['registration']);

        $answer     = new AnswerDiligence();
        $reg        = $app->repo('Registration')->find($class->data['registration']);
        
        if($class->data['idAnswer'] > 0){
            $isNewDiligence = true;
             //Se tiver registro de diligência
             $answerDiligences = App::i()->repo('Diligence\Entities\AnswerDiligence')->find($class->data['idAnswer']);
            
            $diligence  = $repo->findId($class->data['diligence']);
            $answerDiligences->answer = $class->data['answer'];
            $answerDiligences->createTimestamp = new DateTime();
            $answerDiligences->registration = $reg;
            $answerDiligences->status = $class->data['status'];
         
            $save = self::saveEntity($answerDiligences);
            return json_encode(['message' => 'success', 'entityId' => $save['entityId'], 'status' => 200]);
            // dump($save);
            // die;
            //  return self::updateContent($diligenceRepository, $class->data['description'], $regs['reg'], $class->data['status']);
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

    public function cancel(Controller $class) : Json
    {
        $app =  App::i();
        //Buscando diligencia
        $repo       = new DiligenceRepo();
        $diligence  = $repo->findId($this->data['diligence']);
        //Buscando a resposta da diligencia
        $answer = $app->repo('\Diligence\Entities\AnswerDiligence')->findBy( ['diligence' => $diligence]);
        $save = null;
        //Alterando o valor do status
        foreach ($answer as $ans) {
            $ans->status  = 0;
            self::saveEntity($ans);     
        }
        if($save == null){
            return $this->json(['message' => 'success', 'status' => 200], 200);
        }
        return $this->json(['message' => 'error', 'status' => 400], 400);
    }

    static public function vertifyWorkingDays($date, $dias) {
        $currentDate = Carbon::parse($date);
        $daysAdds = 0;
        //Consultando no banco os feriados nacionais cadastrados
        $app = App::i();
        $termsHolidays = $app->repo('Term')->findBy(['taxonomy' => 'holiday']);
        $holidays = array_map(function($term) { return $term->term; }, $termsHolidays);

        // Loop até que todos os dias úteis sejam adicionados
        while ($daysAdds < $dias) {
            // Adiciona 1 dia à data atual
            $currentDate->addDay();
    
            // Verifica se o dia adicionado é um dia útil (segunda a sexta-feira)
            if ($currentDate->isWeekday()) {
                //verificando se a data está no array dos feriados, se tiver nao realiza o incremento
                $holiday = $currentDate->format('m-d');
                if (!in_array($holiday, $holidays)) {
                    $daysAdds++;
                }
            }
        }
    
        return $currentDate;
    }

}