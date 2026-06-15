<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$produto = ['nome_peca' => '', 'tamanho' => 'M', 'cor_predominante' => '', 'preco_venda' => '', 'imagem' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_peca'];
    $tamanho = $_POST['tamanho'];
    $cor = $_POST['cor_predominante'];
    $preco = $_POST['preco_venda'];
    
    // Mantém a imagem antiga por padrão se nenhuma nova for enviada
    $nome_imagem = $produto['imagem']; 

    // Lógica para upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        // Gera um nome exclusivo baseado em timestamp para evitar sobrescrever arquivos
        $nome_imagem = uniqid() . "." . $extensao; 
        
        // Define o caminho da pasta de uploads (subindo um nível para compartilhar entre os escopos)
        $diretorio_destino = '../uploads/';
        
        if (!is_dir($diretorio_destino)) {
            mkdir($diretorio_destino, 0755, true);
        }
        
        // CORRIGIDO: Alterado de tmp_temp para tmp_name para o correto funcionamento do upload
        move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio_destino . $nome_imagem);
    }

    if ($id) {
        $sql = "UPDATE produto SET nome_peca=?, tamanho=?, cor_predominante=?, preco_venda=?, imagem=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $tamanho, $cor, $preco, $nome_imagem, $id]);
    } else {
        $sql = "INSERT INTO produto (nome_peca, tamanho, cor_predominante, preco_venda, imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $tamanho, $cor, $preco, $nome_imagem]);
    }
    header("Location: ProdutoList.php");
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Cadastrar'; ?> Produto</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            
            <ul class="nav nav-tabs mb-4" id="produtoTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="dados" aria-selected="true">
                        <i class="fa-solid fa-file-lines me-1"></i> Dados Gerais
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="imagens-tab" data-bs-toggle="tab" data-bs-target="#imagens" type="button" role="tab" aria-controls="imagens" aria-selected="false">
                        <i class="fa-solid fa-images me-1"></i> Imagens do Produto
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="produtoTabConteudo">
                
                <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome da Peça <span class="text-danger">*</span></label>
                            <input type="text" name="nome_peca" class="form-control" value="<?php echo htmlspecialchars($produto['nome_peca']); ?>" required>
                            <div class="invalid-feedback">O nome da peça é obrigatório.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tamanho <span class="text-danger">*</span></label>
                            <select name="tamanho" class="form-control" required>
                                <option value="P" <?php echo $produto['tamanho'] == 'P' ? 'selected' : ''; ?>>P</option>
                                <option value="M" <?php echo $produto['tamanho'] == 'M' ? 'selected' : ''; ?>>M</option>
                                <option value="G" <?php echo $produto['tamanho'] == 'G' ? 'selected' : ''; ?>>G</option>
                                <option value="GG" <?php echo $produto['tamanho'] == 'GG' ? 'selected' : ''; ?>>GG</option>
                            </select>
                            <div class="invalid-feedback">Selecione um tamanho.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Cor Predominante <span class="text-danger">*</span></label>
                            <input type="text" name="cor_predominante" class="form-control" value="<?php echo htmlspecialchars($produto['cor_predominante']); ?>" required>
                            <div class="invalid-feedback">Defina a cor principal.</div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Preço de Venda (R$) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="preco_venda" class="form-control" value="<?php echo htmlspecialchars($produto['preco_venda']); ?>" required>
                            <div class="invalid-feedback">Insira um preço válido maior que zero.</div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="imagens" role="tabpanel" aria-labelledby="imagens-tab">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Selecione a Imagem do Produto <span class="text-danger">*</span></label>
                            
                            <input type="file" name="imagem" class="form-control" accept="image/*" <?php echo !$id ? 'required' : ''; ?>>
                            
                            <div class="invalid-feedback">A inclusão de uma imagem é obrigatória para cadastrar um novo produto.</div>
                            <div class="form-text">Formatos recomendados: JPG, JPEG ou PNG.</div>
                        </div>
                        <div class="col-md-6 text-center">
                            <?php if (!empty($produto['imagem']) && file_exists('../uploads/' . $produto['imagem'])): ?>
                                <p class="fw-bold mb-1 small text-muted">Imagem Atual:</p>
                                <img src="../uploads/<?php echo $produto['imagem']; ?>" alt="Preview" class="img-thumbnail shadow-sm" style="max-height: 160px; width: 160px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div class="text-muted small py-3">
                                    <i class="fa-solid fa-image fa-3x d-block mb-2 opacity-25"></i>
                                    Nenhuma foto vinculada a este produto.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

            <hr>
            <div class="mt-3">
                <button type="submit" class="btn btn-success px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Salvar Produto</button>
                <a href="ProdutoList.php" class="btn btn-secondary px-4">Cancelar</a>
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

                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    var tabPane = firstInvalid.closest('.tab-pane');
                    if (tabPane) {
                        var id = tabPane.getAttribute('id');
                        var tabButton = document.querySelector('[data-bs-target="#' + id + '"]');
                        if (tabButton) {
                            var tab = new bootstrap.Tab(tabButton);
                            tab.show();
                        }
                    }
                    firstInvalid.focus();
                }
            }

            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include '../footer.php'; ?>