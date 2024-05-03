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

    const STATUS_OPEN = 2; // Para diligencias que está em aberto
    const STATUS_SEND = 3; // Para diligência que foi enviada para o proponente
    const STATUS_ANSWERED = 4; // Para diligências que foi respondido pelo proponente

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
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\Diligence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="diligence_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $diligence;

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
    public function create($class)
    {
        App::i()->applyHook('entity(diligence).createAnswer:before');
        $repo       = new DiligenceRepo();
        $diligence  = $repo->findId($class->data['diligence']);
        $answerDiligences = $repo->findBy('Diligence\Entities\AnswerDiligence', ['diligence' => $diligence]);
        $answer     = new AnswerDiligence();
      
        if(count($answerDiligences) > 0){
            foreach ($answerDiligences as $key => $answerDiligence) {
                $answerDiligence->diligence = $diligence;
                $answerDiligence->answer = $class->data['answer'];
                $answerDiligence->createTimestamp = new DateTime();
                $answerDiligence->status = $class->data['status'];
            }
            $save = self::saveEntity($answerDiligence);
        }else{
            $answer->diligence = $diligence;
            $answer->answer = $class->data['answer'];
            $answer->createTimestamp = new DateTime();
            $answer->status = $class->data['status'];
            $save = self::saveEntity($answer);
        }
        App::i()->applyHook('entity(diligence).createAnswer', [&$answer]);
        return $save;
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
        $dataAtual = Carbon::parse($date); // Obtém a data e hora atual
        $diasAdicionados = 0;
    
        // Loop até que todos os dias úteis sejam adicionados
        while ($diasAdicionados < ($dias - 1)) {
            // Adiciona 1 dia à data atual
          
            // Verifica se o dia adicionado é um dia útil (segunda a sexta-feira)
            if ($dataAtual->isWeekday()) {
                $dt = $dataAtual->addDay();
                dump($dt);
                $diasAdicionados++;
            }
        }
    
        return $dataAtual;
    }

}