<?php
// Inclui a classe de persistência e conexão com o banco de dados
include '../db.class.php';
// Inclui o componente visual do cabeçalho da página
include '../header.php';

// Ativa a conexão com o banco chamando o método conectar() da classe DB
$db = DB::conectar();
// Captura o ID da URL se ele existir (Modo Edição), caso contrário define como null
$id = $_GET['id'] ?? null;
// Inicializa o array do produto com valores padrão, definindo o tamanho padrão como 'M'
$produto = ['nome_peca' => '', 'tamanho' => 'M', 'cor_predominante' => '', 'preco_venda' => '', 'imagem' => ''];

// Traz de volta do banco os dados de um produto já cadastrado para preencher o formulário (Modo Edição)
if ($id) {
    // Prepara uma consulta segura baseada no ID recebido
    $stmt = $db->prepare("SELECT * FROM produto WHERE id = ?");
    // Executa a busca passando o ID como argumento
    $stmt->execute([$id]);
    // Preenche o array $produto com os dados reais recuperados do banco de dados
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
}

// CAPTURA DE DADOS: Se o formulário foi enviado, guarda em variáveis tudo o que o usuário digitou nos campos.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atribui os dados enviados via POST às variáveis locais
    $nome = $_POST['nome_peca'];
    $tamanho = $_POST['tamanho'];
    $cor = $_POST['cor_predominante'];
    $preco = $_POST['preco_venda'];
    
    // Mantém a imagem antiga por padrão se nenhuma nova for enviada
    $nome_imagem = $produto['imagem']; 

    // Lógica para upload da imagem: verifica se um arquivo foi enviado e se não há erros no envio
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        // Extrai a extensão do arquivo original enviado (ex: png, jpg)
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        // Gera um nome exclusivo baseado em timestamp para evitar sobrescrever arquivos
        $nome_imagem = uniqid() . "." . $extensao; 
        
        // Define o caminho da pasta de uploads (subindo um nível para compartilhar entre os escopos)
        $diretorio_destino = '../uploads/';
        
        // Verifica se a pasta de destino existe. Se não existir, cria a pasta com permissões de leitura/escrita
        if (!is_dir($diretorio_destino)) {
            mkdir($diretorio_destino, 0755, true);
        }
        
        // CORRIGIDO: Alterado de tmp_temp para tmp_name para o correto funcionamento do upload
        // Move o arquivo temporário do servidor para a pasta de destino final com o novo nome único
        move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio_destino . $nome_imagem);
    }

// SALVAR NO BANCO: Se o produto já tem ID, atualiza os dados dele (UPDATE). 
// Se não tem ID, cadastra como um produto novo (INSERT). Depois, volta para a listagem.
    if ($id) {
        // SQL de atualização para modificar o registro correspondente ao ID
        $sql = "UPDATE produto SET nome_peca=?, tamanho=?, cor_predominante=?, preco_venda=?, imagem=? WHERE id=?";
        $stmt = $db->prepare($sql);
        // Executa a query injetando os valores na ordem correta, incluindo o ID na cláusula WHERE
        $stmt->execute([$nome, $tamanho, $cor, $preco, $nome_imagem, $id]);
    } else {
        // SQL de inserção para adicionar uma nova linha na tabela produto
        $sql = "INSERT INTO produto (nome_peca, tamanho, cor_predominante, preco_venda, imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        // Executa a gravação do novo produto passando as variáveis correspondentes aos marcadores
        $stmt->execute([$nome, $tamanho, $cor, $preco, $nome_imagem]);
    }
    // Redireciona o usuário para a tela de listagem de produtos
    header("Location: ProdutoList.php");
    // Interrompe o processamento do arquivo atual
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
    'use strict' // Habilita o modo estrito para o interpretador do JavaScript

    // Obtém todos os formulários configurados com a classe 'needs-validation'
    var forms = document.querySelectorAll('.needs-validation')

    // Converte a coleção em array e varre elemento por elemento
    Array.prototype.slice.call(forms).forEach(function (form) {
        // Adiciona o escutador de eventos focado no gatilho de submit do formulário
        form.addEventListener('submit', function (event) {
            // Verifica se o formulário falhou em alguma validação nativa do HTML
            if (!form.checkValidity()) {
                event.preventDefault() // Impede a continuação do envio dos dados
                event.stopPropagation() // Para a propagação do evento na árvore do DOM

                // Localiza o primeiro elemento inválido encontrado dentro do form
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    // Descobre se o elemento inválido está localizado dentro de um container de abas (.tab-pane)
                    var tabPane = firstInvalid.closest('.tab-pane');
                    if (tabPane) {
                        // Obtém o atributo ID do painel invisível que gerou o erro
                        var id = tabPane.getAttribute('id');
                        // Localiza o botão da aba correspondente que controla a visualização desse painel
                        var tabButton = document.querySelector('[data-bs-target="#' + id + '"]');
                        if (tabButton) {
                            // Instancia a API de Tabs do Bootstrap e força a exibição da aba onde está o erro
                            var tab = new bootstrap.Tab(tabButton);
                            tab.show();
                        }
                    }
                    // Direciona o cursor/foco do teclado para o campo inválido
                    firstInvalid.focus();
                }
            }

            // Aplica a classe que sinaliza as cores de validação (sucesso ou falha) nos elementos HTML
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php 
// Inclui o encerramento do arquivo (rodapé padrão)
include '../footer.php'; 
?>