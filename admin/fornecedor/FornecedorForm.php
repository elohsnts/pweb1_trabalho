<?php
// Inclui o arquivo de classe do banco de dados (responsável pela conexão)
include '../db.class.php';
// Inclui o cabeçalho padrão do layout do sistema (HTML inicial, menu, links CSS)
include '../header.php';

// Estabelece a conexão com o banco de dados chamando o método estático da classe DB
$db = DB::conectar();

// Obtém o 'id' da URL via método GET. Se não existir, define como null (operador de coalescência nula)
$id = $_GET['id'] ?? null;

// Inicializa um array com campos vazios para evitar erros de índice indefinido na primeira renderização do formulário
$fornecedor = ['nome_empresa' => '', 'cnpj' => '', 'prazo_entrega_dias' => '', 'telefone_contato' => ''];

// Se um ID foi passado na URL, significa que é uma ação de Edição. Busca os dados atuais no banco.
if ($id) {
    // Prepara a consulta SQL para evitar SQL Injection
    $stmt = $db->prepare("SELECT * FROM fornecedor WHERE id = ?");
    // Executa a consulta passando o ID capturado
    $stmt->execute([$id]);
    // Sobrescreve o array vazio com os dados reais do fornecedor vindos do banco
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verifica se o formulário foi enviado via método POST (Ação de Salvar/Cadastrar/Atualizar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados enviados pelos campos do formulário
    $nome = $_POST['nome_empresa'];
    $cnpj = $_POST['cnpj'];
    $prazo = $_POST['prazo_entrega_dias'];
    $telefone = $_POST['telefone_contato'];

    // Se existir um ID, faz a atualização (UPDATE) do registro existente
    if ($id) {
        $sql = "UPDATE fornecedor SET nome_empresa=?, cnpj=?, prazo_entrega_dias=?, telefone_contato=? WHERE id=?";
        $stmt = $db->prepare($sql);
        // Passa as variáveis na mesma ordem das interrogações (?) do SQL
        $stmt->execute([$nome, $cnpj, $prazo, $telefone, $id]);
    } else {
        // Se NÃO existir um ID, insere um novo registro (INSERT) no banco
        $sql = "INSERT INTO fornecedor (nome_empresa, cnpj, prazo_entrega_dias, telefone_contato) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        // Executa a inserção com os dados do novo fornecedor
        $stmt->execute([$nome, $cnpj, $prazo, $telefone]);
    }
    // Redireciona o usuário de volta para a tela de listagem de fornecedores
    header("Location: FornecedorList.php");
    // Interrompe a execução do script para garantir o redirecionamento imediato
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Cadastrar'; ?> Fornecedor</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nome da Empresa <span class="text-danger">*</span></label>
                    <input type="text" name="nome_empresa" class="form-control" value="<?php echo htmlspecialchars($fornecedor['nome_empresa']); ?>" required>
                    <div class="invalid-feedback">O nome da empresa é obrigatório.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">CNPJ <span class="text-danger">*</span></label>
                    <input type="text" name="cnpj" class="form-control" value="<?php echo htmlspecialchars($fornecedor['cnpj']); ?>" required>
                    <div class="invalid-feedback">O CNPJ é obrigatório.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Prazo Médio de Entrega (em dias) <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="prazo_entrega_dias" class="form-control" value="<?php echo htmlspecialchars($fornecedor['prazo_entrega_dias']); ?>" required>
                    <div class="invalid-feedback">Insira um prazo válido em dias.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Telefone de Contato <span class="text-danger">*</span></label>
                    <input type="text" name="telefone_contato" class="form-control" value="<?php echo htmlspecialchars($fornecedor['telefone_contato']); ?>" required>
                    <div class="invalid-feedback">O telefone de contato é obrigatório.</div>
                </div>
            </div>
            
            <hr>
            <div class="mt-3">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Salvar
                </button>
                <a href="FornecedorList.php" class="btn btn-secondary px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Função autoinvocável (IIFE) para escopar as variáveis e evitar conflitos no escopo global
(function () {
    'use strict' // Ativa o modo estrito do JavaScript para evitar más práticas de codificação

    // Seleciona todos os formulários da página que possuem a classe de validação do Bootstrap
    var forms = document.querySelectorAll('.needs-validation')

    // Converte a lista de nós encontrados em um Array e itera sobre cada formulário encontrado
    Array.prototype.slice.call(forms).forEach(function (form) {
        // Adiciona um ouvinte para interceptar o evento de envio (submit) do formulário
        form.addEventListener('submit', function (event) {
            // Verifica se as validações HTML5 nativas (como o atributo 'required') falharam
            if (!form.checkValidity()) {
                event.preventDefault() // Cancela/bloqueia o envio do formulário para o servidor
                event.stopPropagation() // Impede que o evento se propague para outros elementos
                
                // Dá foco automaticamente no primeiro campo que contiver um erro de validação
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }

            // Adiciona a classe do Bootstrap que exibe visualmente os feedbacks verdes (sucesso) ou vermelhos (erro)
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php 
// Inclui o rodapé padrão do layout do sistema (tags de fechamento HTML, scripts globais)
include '../footer.php'; 
?>