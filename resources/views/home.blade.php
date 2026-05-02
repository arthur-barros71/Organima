<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Organima</title>

    <!--Scripts-->
    <script src="{{ asset('js/LoggedOut.js') }}" defer></script>
    <script src="{{ asset('js/Log-Cad.js') }}" defer></script>
    <script src="{{ asset('js/notfy.js') }}" defer></script>
    <script src="{{ asset('js/Drag.js') }}" defer></script>
    <script src="{{ asset('js/home.js') }}" defer></script>

    <script src="{{ asset('js/modal/profileOptions.js') }}" defer></script>
    <script src="{{ asset('js/modal/profileModal.js') }}" defer></script>
    <script src="{{ asset('js/modal/createProject.js') }}" defer></script>
    <script src="{{ asset('js/modal/editProject.js') }}" defer></script>
    <script src="{{ asset('js/modal/createScript.js') }}" defer></script>
    <script src="{{ asset('js/modal/editScript.js') }}" defer></script>
    <script src="{{ asset('js/modal/deleteProject.js') }}" defer></script>
    <script src="{{ asset('js/modal/deleteScript.js') }}" defer></script>

    <!--Carregar o jQuery-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="profile-image-upload-url" content="{{ route('atualizarFotoPerfil') }}">

    <!--CSS-->
    <link rel="stylesheet" href="{{asset('css/LoggedOut.css')}}">
    <link rel="stylesheet" href="{{asset('css/Log-Cad.css')}}">
    <link rel="stylesheet" href="{{asset('css/pallet.css')}}">
    <link rel="stylesheet" href="{{asset('css/notifications.css')}}">
    <link rel="stylesheet" href="{{asset('css/Int_Img.css')}}">
    <link rel="stylesheet" href="{{asset('css/home.css')}}">
</head>

<body class="body">

    @auth <!-- Loggged in -->

        @php
            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif'];
            $imagePath = null;

            foreach ($extensions as $extension) {
                $imagePath = "user_" . Auth::id() . "/profile{$extension}";
                if (Storage::disk('public')->exists($imagePath)) {
                    break;
                }
                $imagePath = null;
            }
        @endphp

        <div class="aviso-media">
            <img src="image/Max_Min.png">
            <p class="titlefont">Parece que a tela está muito pequena</p>
            <p class="fontcorpo">Use a Organima em uma tela maior</p>
        </div>

        <div class="Logged">

            @include('partials.logged.sideBar')

            <!---------------------------------------Modals----------------------------------------->
            @include('partials.modal.deleteProject') <!-- delete project's modal -->        
            @include('partials.modal.deleteScript') <!-- delete script's modal -->        
            @include('partials.modal.createProject') <!-- create project's modal -->        
            @include('partials.modal.editProject') <!-- edit script's modal -->        
            @include('partials.modal.createScript') <!-- create script's modal -->        
            @include('partials.modal.editScript') <!-- edit script's modal -->        
            @include('partials.modal.editProfile') <!-- edit profile's modal -->        
            @include('partials.modal.profileOptions') <!-- profile options' modal -->


            <!---------------------------------------Pages----------------------------------------->
            <div class="page1_conteudo" id="page1">
                @include('partials.logged.page1Content')
            </div>

            <div class="page2_conteudo" id="page2">
                @include('partials.logged.page2Content')
            </div>

            <div class="page3_conteudo" id="page3">
                @include('partials.logged.page3Content')
            </div>

            <!-- Loading spinner -->
            <div class="load">
                <div class="spinner"></div>
            </div>

        </div>

    @else <!-- Logged out -->
    
        <!---------------------------------------Login and register--------------------------------------->

        <div class="center">

            <!-- Register -->
            @include('partials.registration.register')

            <!-- Center image -->
            <div class="image" id="image">
                <div id="drag" class="gif">
                </div>
                <img src="image/Frame 42.png" id="dragImg" class="gif">
                <div id="dragImg2" class="gif">
                    <!-- Resizing manipulators -->
                    <div class="resize-handle top-left"></div>
                    <div class="resize-handle top-right"></div>
                    <div class="resize-handle bottom-left"></div>
                    <div class="resize-handle bottom-right"></div>
                </div>
            </div>
            
            <!-- Login -->
            @include('partials.registration.login')
            
        </div>

        <!---------------------------------------Initial pages--------------------------------------->
        <div class="centerBegin">

            <!-- Header -->
            @include('partials.home.header')

            <!-- Pages -->
            @include('partials.home.initialPage')
            @include('partials.home.aboutUs')
            @include('partials.home.contactUs')

            <!-- Footer -->
            @include('partials.home.footer')

        </div>

    @endauth <!-- Whatever's outside the auth will appear either the user is logged in or not -->

    <!--Notification
    <div class="notification background">
        <div id="notf_side" class="notf_side back-green"></div>
        <div class="notf_content">
            <img id="notf_img" src="image/Success.svg">
            <div class="message">
                <p class="font notf_title">Ação bem-sucedida</p>
                <p class="font notf_message">Mensagem</p>
            </div>
        </div>
    </div>
    -->

</body>

</html>