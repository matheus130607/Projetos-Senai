// Pagina Usuario

        function Login() {
            window.location.href = "../View/login.php"; 
        
        }

        function plano() {
                    // Navega para a página principal e posiciona na âncora dos planos.
                    // Constroi o caminho baseado no diretório atual para evitar problemas relativos.
                    try {
                        var href = window.location.href;
                        var base = href.substring(0, href.lastIndexOf('/') + 1);
                        window.location.href = base + 'Pag_Inicial_CL.html#planos-container';
                    } catch (e) {
                        // fallback simples
                        window.location.href = 'Pag_Inicial_CL.html#planos-container';
                    }
        }

        function faleconosco() {
                    // Encontrar o elemento de links do footer e rolar suavemente até ele
                    var el = document.getElementById('links-footer') || document.getElementById('footer');
                    if (el && el.scrollIntoView) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        // fallback simples
                        location.href = "#links-footer";
                    }
        }

        function loja() {
            window.location.href = "loja.html"
        }

        function home() {
            window.location.href = "Pag_Inicial_CL.html"
        }

        function entrar() {
            window.location.href = "Pag_Inicial_CL.html"
        }

        function entrar_login() {
            window.location.href = "login.html"
        }

        function modalidades() {
            window.location.href = "modalidades.html"
        }

        function franquias() {
            window.location.href = "franquias.html"
        }

        function saibamais_franc() {
            window.location.href = "saibamais_franc.html"
        }

        function perfil_usuario() {
            window.location.href = "perfil_usuario.php"
        }
        