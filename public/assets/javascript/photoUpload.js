const imageGrid = document.getElementById("imageGrid");

for (let i = 0; i < 10; i++) {
    const card = document.createElement("div");
    card.className = "card";

    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.id = `upload${i}`;

    // Label and icon creation
    const label = document.createElement("label");
    label.htmlFor = input.id;
    label.className = "icon-btn";

    const icon = document.createElement("img");
    icon.width = 20;

    // If first two — preload images
    if (i < 2) {
        const img = document.createElement("img");
        img.src = i === 0
            ? "assets/images/add-photo-screen/person1.jpg"
            : "assets/images/add-photo-screen/person2.jpg";
        img.className = "preview";
        card.appendChild(img);

        // Show gallery icon only if image exists
        icon.src = "assets/images/svg/camera.svg";
        label.appendChild(icon);
        card.appendChild(input);
        card.appendChild(label);

        card.style.border = "none";
    }
    else {
        // For empty slots (no image yet), no icon shown initially
        card.appendChild(input);

        const addIcon = document.createElement("div");
        addIcon.className = "add-icon";
        addIcon.innerHTML = '<img src="assets/images/svg/gallery-icon.svg" width="30">';
        const addLabel = document.createElement("div");
        addLabel.className = "add-label";
        addLabel.innerText = "Add";
        card.appendChild(addIcon);
        card.appendChild(addLabel);

        // Clicking card triggers file input
        card.addEventListener("click", () => input.click());
    }

    // File upload logic
    input.addEventListener("change", () => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.createElement("img");
                preview.src = e.target.result;
                preview.className = "preview";

                while (card.firstChild) card.removeChild(card.firstChild);
                card.appendChild(preview);
                card.appendChild(input);

                // Remove dashed border
                card.style.border = "none";

                const newLabel = document.createElement("label");
                newLabel.htmlFor = input.id;
                newLabel.className = "icon-btn";

                const newIcon = document.createElement("img");
                newIcon.src = "assets/images/svg/camera.svg";
                newIcon.width = 20;
                newLabel.appendChild(newIcon);

                card.appendChild(newLabel);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });


    imageGrid.appendChild(card);
}