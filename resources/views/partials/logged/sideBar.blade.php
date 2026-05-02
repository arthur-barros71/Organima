<!--barra lateral-->
<div class="Lateral-bar back-black fontcorpo">
    <div class="bar-margin">
        <div class="profile" onclick="showProfileModal()">
            <div class="prf">

                <div class="profileImgBar">
                    <img src="{{ $imagePath ? url('storage/' . $imagePath) : asset('image/ProfileImg.svg') }}"
                        id="profileImgBar">
                </div>

                <p id="NameBar" class="white-text fontcorpo">{{ Auth::user()->nm_usuario }}</p>
            </div>
            <img id="profileSeta" src="image/SetaRight.svg">
        </div>
        <div class="pages">
            <div class="item" onclick="changePage(1)">
                <div class="row">
                    <svg class="itemImg" id="homeIcon" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path class="svgPath"
                            d="M1.78663 17.9715C0.84194 17.9715 0.00839043 17.0904 0.00839043 15.9656C-0.0471802 15.4032 0.1751 14.9033 0.619659 14.4659L14.8122 0.437415C15.1456 0.124976 15.5902 0 15.9792 0C16.3682 0 16.8127 0.0624878 17.2017 0.499902L31.4443 14.4597C31.8333 14.8971 32 15.397 32 15.9594C32 17.0904 31.222 17.9652 30.2218 17.9652H28.4435V22.3206C28.4491 22.3769 28.4491 22.4394 28.4491 22.4956V29.4942C28.4491 30.8752 27.4544 31.9938 26.2263 31.9938H25.3372C25.2705 31.9938 25.2038 31.9875 25.1372 31.9813L25.1371 31.9813L25.137 31.9813C25.0537 31.9875 24.9704 31.9938 24.887 31.9938H23.1144H16H8.88847L7.08245 32H7.0823C7.00455 32 6.9268 32 6.84905 31.9938C6.78796 32 6.72687 32 6.66578 32H6.66567H5.77655C4.54845 32 3.55375 30.8815 3.55375 29.5005V28.4882C3.53708 28.3195 3.52597 28.1508 3.52597 27.982L3.56487 17.9715H1.78663Z"
                            fill="#FDFDFD" />
                    </svg>
                    <a class="ItemText fontcorpo">Página inicial</a>
                </div>
                <div class="PageLine1" id="page1_bar"></div>
            </div>
            <div class="item" onclick="changePage(2)">
                <div class="row">
                    <svg class="itemImg" id="projIcon" viewBox="0 0 32 40" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path class="svgPath"
                            d="M1.20332 10.7143C0.7584 10.7143 0.535074 10.1768 0.848975 9.86152L9.81233 0.858154C10.127 0.54209 10.6667 0.76493 10.6667 1.21092V10.7143L12.8 12.8571V1C12.8 0.447715 13.2477 0 13.8 0H31C31.5523 0 32 0.447715 32 1V39C32 39.5523 31.5523 40 31 40H1C0.447715 40 0 39.5523 0 39V13.8571C0 13.3049 0.447715 12.8571 1 12.8571H12.8L10.6667 10.7143H1.20332Z"
                            fill="#FDFDFD" />
                    </svg>
                    <a class="ItemText fontcorpo">Projetos</a>
                </div>
                <div class="PageLine1" id="page2_bar"></div>
            </div>
            <div class="item" onclick="changePage(3)">
                <div class="row">
                    <svg class="itemImg" id="rotIcon" viewBox="0 0 36 36" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path class="svgPath"
                            d="M28.8486 16.2457L29.6431 15.4512L27.2595 13.0676L22.8931 8.70117L20.5095 6.31758L19.715 7.11211L18.1259 8.70117L4.11969 22.7074C3.38844 23.4387 2.85407 24.3457 2.55875 25.3371L0.0696918 33.8027C-0.106089 34.3934 0.0556292 35.0332 0.498598 35.4691C0.941567 35.9051 1.57438 36.0668 2.165 35.898L10.6236 33.409C11.615 33.1137 12.522 32.5793 13.2533 31.848L27.2595 17.8418L28.8486 16.2457ZM11.2494 28.0863L10.6095 29.6824C10.3283 29.9004 10.0119 30.0621 9.67438 30.1676L4.17594 31.7848L5.79313 26.2934C5.89157 25.9488 6.06032 25.6324 6.27829 25.3582L7.87438 24.7184V26.9684C7.87438 27.5871 8.38063 28.0934 8.99938 28.0934H11.2494V28.0863ZM25.5017 1.31836L24.4892 2.33789L22.9002 3.92695L22.0986 4.72148L24.4822 7.10508L28.8486 11.4715L31.2322 13.8551L32.0267 13.0605L33.6158 11.4715L34.6353 10.452C36.3931 8.69414 36.3931 5.84648 34.6353 4.08867L31.872 1.31836C30.1142 -0.439453 27.2666 -0.439453 25.5088 1.31836H25.5017ZM22.1689 13.1309L12.0439 23.2559C11.608 23.6918 10.8908 23.6918 10.4548 23.2559C10.0189 22.8199 10.0189 22.1027 10.4548 21.6668L20.5798 11.5418C21.0158 11.1059 21.733 11.1059 22.1689 11.5418C22.6048 11.9777 22.6048 12.6949 22.1689 13.1309Z"
                            fill="#FDFDFD" />
                    </svg>
                    <a class="ItemText fontcorpo">Blocos de roteiro</a>
                </div>
                <div class="PageLine1" id="page3_bar"></div>
            </div>
        </div>
        <div class="logout back-black" action="/sair" method="POST">

        </div>
    </div>
</div>
