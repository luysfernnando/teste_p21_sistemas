// Funções de Utilidade
function showLoading() {
    $('.loading').fadeIn();
}

function hideLoading() {
    $('.loading').fadeOut();
}

function showAlert(message, type = 'success') {
    const alert = `
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container').prepend(alert);
    
    // Auto-hide após 5 segundos
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

// Confirmação de Exclusão
function confirmDelete(event, message = 'Tem certeza que deseja excluir este item?') {
    if (!confirm(message)) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Máscaras de Input
function initInputMasks() {
    // Telefone
    $('input[data-mask="phone"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        } else {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        }
        $(this).val(value);
    });
}

// Validação de Formulários
function validateForm(form) {
    let isValid = true;
    
    // Limpa mensagens de erro anteriores
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();
    
    // Valida campos required
    form.find('[required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            $(this).after(`<div class="invalid-feedback">Este campo é obrigatório.</div>`);
            isValid = false;
        }
    });
    
    // Valida emails
    form.find('input[type="email"]').each(function() {
        const email = $(this).val();
        if (email && !isValidEmail(email)) {
            $(this).addClass('is-invalid');
            $(this).after(`<div class="invalid-feedback">Email inválido.</div>`);
            isValid = false;
        }
    });
    
    return isValid;
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Inicialização
$(document).ready(function() {
    // Inicializa máscaras
    initInputMasks();
    
    // Validação de formulários
    $('form').on('submit', function(e) {
        if (!validateForm($(this))) {
            e.preventDefault();
        }
    });
    
    // Inicializa tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializa popovers do Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Adiciona confirmação para links de exclusão
    $('a[data-confirm], button[data-confirm]').on('click', function(e) {
        const message = $(this).data('confirm') || 'Tem certeza que deseja realizar esta ação?';
        if (!confirmDelete(e, message)) {
            return false;
        }
    });
}); 