// Pagina Usuario

        function Login() {
            window.location.href = "login.html"; 
        
        }

        function plano() {
            location.href ="Pag_Inicial_CL.html #planos-container";
        }

        function faleconosco() {
            location.href ="#links-footer"
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


    

// Pagina Modalidades

        function Unidade(secao) {
            document.getElementById('boxe').classList.add('hidden');
            document.getElementById('pilates').classList.add('hidden');


            document.getElementById(secao).classList.remove('hidden');
        }

       
