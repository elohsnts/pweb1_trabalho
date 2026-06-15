<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$fornecedor = ['nome_empresa' => '', 'cnpj' => '', 'prazo_entrega_dias' => '', 'telefone_contato' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_empresa'];
    $cnpj = $_POST['cnpj'];
    $prazo = $_POST['prazo_entrega_dias'];
    $telefone = $_POST['telefone_contato'];

    if ($id) {
        $sql = "UPDATE fornecedor SET nome_empresa=?, cnpj=?, prazo_entrega_dias=?, telefone_contato=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $cnpj, $prazo, $telefone, $id]);
    } else {
        $sql = "INSERT INTO fornecedor (nome_empresa, cnpj, prazo_entrega_dias, telefone_contato) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $cnpj, $prazo, $telefone]);
    }
    header("Location: FornecedorList.php");
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
(function () {
    'use strict'

    var forms = document.querySelectorAll('.needs-validation')

    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                
                // Dá foco automaticamente no primeiro campo com erro
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }

            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include '../footer.php'; ?>