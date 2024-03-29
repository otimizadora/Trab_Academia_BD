<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../classes/pessoa.php';
include_once 'dao.php';

class AlunoDao extends Dao {

    function salvar($pessoa) {
        $type_user = "Aluno";
        $cargo = "0";
        $pdo = Dao::getInstance();
        $pdo->beginTransaction();
        $sql = $sql = "CALL create_user(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,? ,? ,? ,?, ?, ?, ?, ?);";
        $stmt = $pdo->prepare($sql);
        $valores = array($type_user, $pessoa->getCpf(),$pessoa->getNome(), $pessoa->getSobrenome(), $pessoa->getSexo(), $pessoa->getDataNascimento(), $pessoa->getEndereco(), $pessoa->getUf(), $pessoa->getCidade(), $pessoa->getBairro(), $pessoa->getRg(), $pessoa->getEmail(), $pessoa->getTelefoneResidencial(), $pessoa->getTelefoneCelular(), $pessoa->getPesoInicial_Aluno(), $pessoa->getDataEntrada_Aluno(),$pessoa->getNome(),$pessoa->getSenha(),$cargo);
       
            // ignore esse comentario: somente para testes: echo '='.$dados[0]['Mensagem'] . '<br>' ;
        if ($stmt->execute($valores)) {
            $pdo->commit();
            return "Aluno salvo com sucesso! O login do usuário é o CPF e não poderá ser alterado.";
        } else {
            $pdo->rollBack();
            return "Ocorreu um erro!";
        }
                         
    }

    function listar() {
        $pdo = Dao::getInstance();
        $sql = "SELECT p.cpf, p.nome, p.sobrenome, p.sexo, p.data_nascimento, p.rg, p.telefone_residencial, p.telefone_celular,p.endereco,p.uf, p.cidade, p.bairro, p.email, a.data_entrada, a.peso_inicial FROM tb_pessoa p INNER JOIN tb_aluno a ON p.cpf = a.id_pessoa_cpf ORDER BY p.nome ASC";
        $result = $pdo->prepare($sql);
        $result->execute();

        $alunos = array();

        foreach ($result->fetchAll() as $linhaConsulta) {
            $pessoa = new Pessoa();
            $pessoa->setCpf($linhaConsulta[0]);
            $pessoa->setNome($linhaConsulta[1] . " " . $linhaConsulta[2]);
            $pessoa->setSexo($linhaConsulta[3]);
            $pessoa->setDataNascimento(date_format(date_create($linhaConsulta[4]), 'd/m/Y'));
            $pessoa->setRg($linhaConsulta[5]);
            $pessoa->setTelefoneResidencial($linhaConsulta[6] . " / " . $linhaConsulta[7]);
            $pessoa->setEndereco($linhaConsulta[8]);
            $pessoa->setUf($linhaConsulta[9]);
            $pessoa->setCidade($linhaConsulta[10]);
            $pessoa->setBairro($linhaConsulta[11]);
            $pessoa->setEmail($linhaConsulta[12]);
            $pessoa->setDataEntrada_Aluno(date_format(date_create($linhaConsulta[13]), 'd/m/Y'));
            $pessoa->setPesoInicial_Aluno(str_replace(".", ",", $linhaConsulta[14]));

            $alunos[] = $pessoa;
        }

        return $alunos;
    }

    public function excluir($pessoa) {
        $pdo = Dao::getInstance();
        $pdo->beginTransaction();

        $sql3 = "DELETE FROM tb_agendamento WHERE id_pessoa_cpf = ?";
        $stmt3 = $pdo->prepare($sql3);

        $sql = "DELETE FROM usuario WHERE id_pessoa_cpf = ?";
        $stmt = $pdo->prepare($sql);

        $sql1 = "DELETE FROM tb_aluno WHERE id_pessoa_cpf = ?";
        $stmt1 = $pdo->prepare($sql1);

        $sql2 = "DELETE FROM tb_pessoa WHERE cpf = ?";
        $stmt2 = $pdo->prepare($sql2);

        if ($stmt3->execute(array($pessoa->getCpf())) == TRUE && $stmt->execute(array($pessoa->getCpf())) == TRUE && $stmt1->execute(array($pessoa->getCpf())) == TRUE && $stmt2->execute(array($pessoa->getCpf())) == TRUE) {
            $pdo->commit();
            return "Aluno excluído com sucesso!";
        } else {
            $pdo->rollBack();
            return "Ocorreu um erro!";
        }
    }

    public function alterar($pessoa) {
        $pdo = Dao::getInstance();
        $pdo->beginTransaction();
        $sql = "UPDATE tb_pessoa SET nome = ?, ";
        $sql .= " sobrenome = ?, sexo = ?,  data_nascimento = ?, endereco = ?, uf = ?,";
        $sql .= " cidade = ?, bairro = ?, rg = ?, telefone_residencial = ?, telefone_celular = ?, email = ?";
        $sql .= " WHERE cpf = ? ";
        $stmt = $pdo->prepare($sql);
        $valores = array($pessoa->getNome(), $pessoa->getSobrenome(), $pessoa->getSexo(), $pessoa->getDataNascimento(), $pessoa->getEndereco(), $pessoa->getUf(), $pessoa->getCidade(), $pessoa->getBairro(), $pessoa->getRg(), $pessoa->getTelefoneResidencial(), $pessoa->getTelefoneCelular(), $pessoa->getEmail(), $pessoa->getCpf());

        $sql1 = "UPDATE tb_aluno SET peso_inicial = ? ";
        $sql1 .= "WHERE id_pessoa_cpf = ? ";
        $stmt1 = $pdo->prepare($sql1);
        $valores1 = array($pessoa->getPesoInicial_Aluno(), $pessoa->getCpf());

        $sql2 = "UPDATE usuario SET usuario = ? , senha = ?, perfil_idperfil = ? ";
        $sql2 .= "WHERE id_pessoa_cpf = ? ";
        $stmt2 = $pdo->prepare($sql2);
        $valores2 = array($pessoa->getNome(), $pessoa->getSenha(), $pessoa->getIdPerfil(), $pessoa->getCpf());


        if ($stmt->execute($valores) == TRUE && $stmt1->execute($valores1) == TRUE && $stmt2->execute($valores2) == TRUE) {
            $pdo->commit();
            return "Aluno alterado com sucesso!";
        } else {
            $pdo->rollBack();
            return "Ocorreu um erro ao tentar alterar o Aluno";
        }
    }

    public function carregar($idAluno) {
        $pdo = Dao::getInstance();
        $sql = "SELECT p.cpf, p.nome, p.sobrenome, p.sexo, p.data_nascimento, p.rg, p.telefone_residencial, p.telefone_celular,p.endereco,p.uf, p.cidade, p.bairro, p.email, a.peso_inicial FROM tb_pessoa p INNER JOIN tb_aluno a ON p.cpf = a.id_pessoa_cpf WHERE a.id_pessoa_cpf = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($idAluno));

        $linhaBanco = $stmt->fetch();

        $pessoa = new Pessoa();
        $pessoa->setCpf($linhaBanco[0]);
        $pessoa->setNome($linhaBanco[1]);
        $pessoa->setSobrenome($linhaBanco[2]);
        $pessoa->setSexo($linhaBanco[3]);
        $pessoa->setDataNascimento($linhaBanco[4]);
        $pessoa->setRg($linhaBanco[5]);
        $pessoa->setTelefoneResidencial($linhaBanco[6]);
        $pessoa->setTelefoneCelular($linhaBanco[7]);
        $pessoa->setEndereco($linhaBanco[8]);
        $pessoa->setUf($linhaBanco[9]);
        $pessoa->setCidade($linhaBanco[10]);
        $pessoa->setBairro($linhaBanco[11]);
        $pessoa->setEmail($linhaBanco[12]);
        $pessoa->setPesoInicial_Aluno(str_replace(".", ",", $linhaBanco[13]));

        return $pessoa;
    }

    public function editapeso($pessoa) {
        $pdo = Dao::getInstance();
        $sql = "UPDATE `tb_aluno` SET `peso_durante` = ? WHERE `tb_aluno`.`id_pessoa_cpf` = ?;";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute(array($pessoa->getPesoDurante_Aluno(), $pessoa->getCpf())) == TRUE) {
            return "Peso alterado com sucesso!";
        } else {
            return "Ocorreu um erro!";
        }
    }

}
