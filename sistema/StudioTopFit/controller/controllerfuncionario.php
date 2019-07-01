<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../classes/pessoa.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../dao/funcionariodao.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../classes/funcionario_e_professor.php';

$operacao = $_GET['operacao'];
$controller = new ControllerFuncionario();

if ($operacao == 'salvar') {
    $controller->salvarFuncionario();
} else if ($operacao == 'excluir') {
    $controller->excluirFuncionario();
} else if ($operacao == 'alterar') {
    $controller->alterarFuncionario();
}

class ControllerFuncionario {

    private $dao;

    public function __construct() {
        $this->dao = new FuncionarioDao();
    }

    public function salvarFuncionario() {
        $pessoa = new Pessoa();
        $pessoa->setNome($_POST['nome']);
        $pessoa->setSobrenome($_POST['sobrenome']);
        $pessoa->setSexo($_POST['sexo']);
        $pessoa->setDataNascimento($_POST['ano'] . "-" . $_POST['mes'] . "-" . $_POST['dia']);
        $pessoa->setEndereco($_POST['endereco']);
        $pessoa->setUf($_POST['uf']);
        $pessoa->setCidade($_POST['cidade']);
        $pessoa->setBairro($_POST['bairro']);
        $pessoa->setCpf($_POST['cpf']);
        $pessoa->setRg($_POST['rg']);
        $pessoa->setTelefoneCelular($_POST['telefoneResidencial']);
        $pessoa->setTelefoneResidencial($_POST['telefoneCelular']);
        $pessoa->setEmail($_POST['email']);
        $pessoa->setSenha($_POST['confirmeSenha']);
        $pessoa->setIdPerfil(2);
        
        $funcionario = new Funcionario();
        $funcionario->setSalario(str_replace(",", ".", $_POST['salario']));
        $funcionario->setCargo($_POST['cargo']);
        $funcionario->setMatricula($_POST['dia'] . substr($_POST['cpf'] , -5) . substr($_POST['ano'] , -2));
        
        $professor = new Professor;
        $professor->setProf($_POST['prof']);
        
        include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../dao/agendabusca.php';
        $agendaBusca = new AgendaBusca();
        $aulas = $agendaBusca->verificarcpf($pessoa->getCpf());
 
        $o = "10";
        $a ="11";
        if ($aulas == 1) {
            $mensagem = "Não foi possível cadastrar funcionário! O CPF informado já está cadastrado.";
        } else {
            if(strcmp($pessoa->getTelefoneCelular() , $pessoa->getTelefoneResidencial()) == 0){
                $mensagem =  "Não foi possível cadastrar funcionário! Os Telefones informados devem ser diferentes.";
            }else{
                $mensagem = $this->dao->salvar($pessoa, $funcionario, $professor);
            }
        }
        header("location: /StudioTopFit/funcionario/listar.php?msg=" . $mensagem);
    }

    public function excluirFuncionario() {
        $pessoa = new Pessoa();
        $pessoa->setCpf($_GET['idfuncionario']);

        $mensagem = $this->dao->excluir($pessoa);

        header("location: /StudioTopFit/funcionario/listar.php?msg=" . $mensagem);
    }

    public function alterarFuncionario() {
        $pessoa = new Pessoa();
        $pessoa->setNome($_POST['nome']);
        $pessoa->setSobrenome($_POST['sobrenome']);
        $pessoa->setSexo($_POST['sexo']);
        $pessoa->setDataNascimento($_POST['ano'] . "-" . $_POST['mes'] . "-" . $_POST['dia']);
        $pessoa->setEndereco($_POST['endereco']);
        $pessoa->setUf($_POST['uf']);
        $pessoa->setCidade($_POST['cidade']);
        $pessoa->setBairro($_POST['bairro']);
        $pessoa->setCpf($_POST['cpf']);
        $pessoa->setRg($_POST['rg']);
        $pessoa->setTelefoneCelular($_POST['telefoneResidencial']);
        $pessoa->setTelefoneResidencial($_POST['telefoneCelular']);
        $pessoa->setEmail($_POST['email']);
        $pessoa->setCargo_Funcionario($_POST['cargo']);
        $pessoa->setSenha($_POST['confirmeSenha']);
        $pessoa->setIdPerfil(2);

        $mensagem = $this->dao->alterar($pessoa);

        header("location: /StudioTopFit/funcionario/listar.php?msg=" . $mensagem);
    }

}
?>


