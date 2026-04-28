/*--------------------------- Page Loader --------------------------------*/
$(function () {
    setTimeout(() => {
        $('.page-loader').fadeOut('slow');
    }, 800);
});
/*---------------------------- Onboarding Screen ----------------------------*/
$(document).on("click", ".skip_btn_1", function () {
    $("#first").removeClass("active");
    $(".first_slide").removeClass("active");

    $("#second").addClass("active");
    $(".second_slide").addClass("active");
});

$(document).on("click", ".skip_btn_2", function () {
    $("#second").removeClass("active");
    $(".second_slide").removeClass("active");

    $("#third").addClass("active");
    $(".third_slide").addClass("active");
});

/*------------------------- New Password hide show button --------------------------*/
$(document).on("click", ".eye-off", function () {
    const input = $(this).siblings("input");
    const isPassword = input.attr("type") === "password";

    input.attr("type", isPassword ? "text" : "password");
    $(this).attr("src", isPassword ? "assets/images/svg/eye.svg" : "assets/images/svg/eye-off.svg");
});

/*------------------------------ Sticky Header -----------------------------*/
$(window).on("scroll", function () {
    const scrollPosition = $(window).scrollTop();

    if (scrollPosition >= 20) {
        $("#top-header, #top-navbar").addClass("fixed");
        $(".Amigo_img_main").css("padding-top", "70px");
    } else {
        $("#top-header, #top-navbar").removeClass("fixed");
        $(".Amigo_img_main").css("padding-top", "0");
    }
});
/*---------------------------- Confirm OTP Input filed  ------------------------------*/
function validateInput(input) {
    input.value = input.value.replace(/\D/g, "");

    if (input.value.length > 1) {
        input.value = input.value.charAt(0);
    }

    if (input.value !== "") {
        input.classList.add("filled");

        const nextInput = input.nextElementSibling;
        if (nextInput && nextInput.tagName === "INPUT") {
            nextInput.focus();
        }
    } else {
        input.classList.remove("filled");
    }
}

/*------------------------ Data Collect Buttons --------------------------*/
document.addEventListener("DOMContentLoaded", () => {
    const toggleButtons = document.querySelectorAll(".toggle-btn-per-info");
    const sections = document.querySelectorAll("section");
    const continueButtons = document.querySelectorAll(".per-arrow-btn");

    function toggleSection(targetSection) {
        sections.forEach((section) => section.classList.remove("active"));

        const targetElement = document.querySelector(`.${targetSection}`);
        if (targetElement) targetElement.classList.add("active");

        toggleButtons.forEach((btn) => btn.classList.remove("active"));
        const targetButton = document.querySelector(`.toggle-btn-per-info[data-section="${targetSection}"]`);
        if (targetButton) targetButton.classList.add("active");
    }

    toggleButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const targetSection = button.dataset.section;
            toggleSection(targetSection);
        });
    });

    continueButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const targetSection = button.dataset.section;
            toggleSection(targetSection);
        });
    });

    toggleSection("personal-info-sec-one");
});

/*-----------------------------  Profile Details Photo Upload -------------------------*/
$(document).on("DOMContentLoaded", function () {
    const readURL = (input) => {
        if (input.files && input.files.length > 0) {
            const reader = new FileReader();
            reader.onload = (e) => {
                $(".profile-pic").attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    $(document).on("change", ".file-upload", function () {
        readURL(this);
    });

    $(document).on("click", ".upload-button", function () {
        $(".file-upload").click();
    });
});

/*-------------------------- Filter Range  -----------------------------*/
document.addEventListener("DOMContentLoaded", function () {
    const minSlider = document.getElementById("min-slider");
    const maxSlider = document.getElementById("max-slider");
    const minPrice = document.getElementById("min-price");
    const maxPrice = document.getElementById("max-price");
    const slider = document.querySelector(".slider");

    if (!minSlider || !maxSlider || !minPrice || !maxPrice || !slider) return;

    function updateSlider() {
        const minVal = parseInt(minSlider.value, 10);
        const maxVal = parseInt(maxSlider.value, 10);

        if (minVal > maxVal) {
            minSlider.value = maxVal;
        }
        minPrice.textContent = `$${minSlider.value}`;

        if (maxVal < minVal) {
            maxSlider.value = minVal;
        }
        maxPrice.textContent = `$${maxSlider.value}`;

        const leftPosition = (parseInt(minSlider.value, 10) / 100) * 100;
        const rightPosition = (parseInt(maxSlider.value, 10) / 100) * 100;

        slider.style.setProperty("--left", `${leftPosition}%`);
        slider.style.setProperty("--right", `${rightPosition}%`);

        minPrice.style.left = `${leftPosition}%`;
        maxPrice.style.left = `${rightPosition}%`;
    }

    minSlider.addEventListener("input", updateSlider);
    maxSlider.addEventListener("input", updateSlider);

    updateSlider();
});

/*------------------------------------- Faq Screen -------------------------------------*/
$(document).ready(function () {
    $('.nested-accordion').find('.comment').slideUp();

    $('.nested-accordion').find('h3').click(function () {
        let $this = $(this);
        let $parentAccordion = $this.closest('.nested-accordion');

        $parentAccordion.siblings().find('.comment').slideUp();
        $parentAccordion.siblings().find('h3').removeClass('selected');

        $this.next('.comment').slideToggle(100);
        $this.toggleClass('selected');

        $parentAccordion.find('.nested-accordion .comment').slideUp();
        $parentAccordion.find('.nested-accordion h3').removeClass('selected');
    });
});

/*---------------------- Id Proof Upload Screen ----------------*/
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('document-upload');
    const filePreview = document.getElementById('file-preview');

    if (fileInput && filePreview) {
        fileInput.addEventListener('change', function () {
            if (this.files.length) {
                handleFiles(this.files);
            }
        });

        function handleFiles(files) {
            filePreview.innerHTML = '';
            filePreview.classList.add('active');

            Array.from(files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-preview-item';

                fileItem.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg>
                    <span class="file-preview-name">${file.name}</span>
                    <span class="file-preview-remove" onclick="this.parentNode.remove()">×</span>
                `;

                filePreview.appendChild(fileItem);
            });
        }
    }
});

/*--------------------------------- chat camera doc pop  -------------------------------------*/
const attachmentButtons = document.querySelectorAll('.buttonAttachment');
const attachmentPopup = document.querySelector('.attachment-popup');

if (attachmentButtons.length > 0 && attachmentPopup) {
    attachmentButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            btn.classList.toggle('active');
            attachmentPopup.classList.toggle('visible');
        });
    });

    document.addEventListener('click', (e) => {
        if (!attachmentPopup.contains(e.target)) {
            attachmentButtons.forEach(btn => btn.classList.remove('active'));
            attachmentPopup.classList.remove('visible');
        }
    });

    document.querySelectorAll('.attachment-option').forEach(option => {
        option.addEventListener('click', () => {
            const action = option.id.replace('-option', '');
        });
    });
}

/*--------------------------- Delete Or Deactive Button Check Screen -------------------------------*/
function continueAction() {
    const form = document.getElementById('deleteDeactivateForm');
    const selectedAction = form.querySelector('input[name="action"]:checked');

    if (!selectedAction) {
        alert("Please select an option before continuing.");
        return;
    }

    if (selectedAction.value === 'delete') {
        window.location.href = 'delete.html';
    } else if (selectedAction.value === 'deactivate') {
        window.location.href = 'deactivate.html';
    }
}

/*------------- Modal And Search Bar Show ---------------*/
document.addEventListener('DOMContentLoaded', function () {
    const moreButton = document.getElementById('moreButton');
    const morePopup = document.getElementById('morePopup');
    const searchButton = document.getElementById('searchButton');
    const searchBar = document.getElementById('searchBar');

    if (moreButton && morePopup) {
        moreButton.addEventListener('click', function (e) {
            e.preventDefault();
            morePopup.classList.toggle('hidden');
        });
    }

    if (searchButton && searchBar) {
        searchButton.addEventListener('click', function () {
            searchBar.classList.remove('hidden');
        });
    }

    document.addEventListener('click', function (e) {
        if (moreButton && morePopup) {
            if (!moreButton.contains(e.target) && !morePopup.contains(e.target)) {
                morePopup.classList.add('hidden');
            }
        }
        if (searchButton && searchBar) {
            if (!searchButton.contains(e.target) && !searchBar.contains(e.target) && e.target !== searchButton) {
                searchBar.classList.add('hidden');
            }
        }
    });
});

/*------------- Modal And Pop Up Export ---------------*/
$(document).ready(function () {
    $('a[href="#finger-print-modal"]').click(function (e) {
        e.preventDefault();

        $('#finger-print-modal').modal('show');

        let counter = 0;
        const counterElement = $('#counter');
        const interval = setInterval(function () {
            counter++;
            counterElement.text(counter + '%');

            if (counter >= 100) {
                clearInterval(interval);

                $('#finger-print-modal').modal('hide');

                setTimeout(function () {
                    var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasBottom'));
                    offcanvas.show();
                }, 500);
            }
        }, 30);
    });

    // Reset counter when modal is closed manually
    $('#finger-print-modal').on('hidden.bs.modal', function () {
        $('#counter').text('0%');
    });
});

/*------------------------------------- Add Home Screen Pop Up Screen -------------------------------------*/
$(document).ready(function () {
    setTimeout(() => {
        $("#bkgOverlay, #delayedPopup").fadeIn(400);
    }, 4800);

    $("#btnClose").on("click", function (e) {
        e.preventDefault();
        HideDialog();
    });

    function HideDialog() {
        $("#bkgOverlay, #delayedPopup").fadeOut(400);
    }
});
