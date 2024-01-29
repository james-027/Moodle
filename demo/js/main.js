document.addEventListener("DOMContentLoaded", function () {

//    //validation fields 
//   $(document).on("click", "#id_submitbutton", function (e) {
//     // Your validation logic here
//     let userEmail = $("#id_user_email").val();
//     let userName = $("#id_user_name").val();
//     let userPhone = $("#id_phone_number").val();
//     let userPicture = $("#picture").val();

//     if (
//       userEmail.trim() === "" ||
//       userName.trim() === "" ||
//       userPhone.trim() === "" ||
//       userPicture.trim() === ""
//     ) {
//       alert("Please fill in all required fields before submitting.");
//       e.preventDefault();
//     } else {
//       // Allow the form to submit
//       // Optionally, you can perform additional validation or processing here
//     }
//   });


//   //email validation
//   let form = document.querySelector(".mform"); // Update with form selector
//   form.enctype = "multipart/form-data";
//   // Use the actual class of your form
//   const cancelButton = document.getElementById("id_cancel"); // Use the actual ID of your cancel button
//   // Function to validate email format
//   function validateEmail(email) {
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     return emailRegex.test(email);
//   }

//   // Event listener for form submission
//   form.addEventListener("submit", function (event) {
//     const emailInput = document.getElementById("id_user_email");
//     const userEmailInput = emailInput.value;

//     // Check if the cancel button is clicked
//     if (event.submitter === cancelButton) {
//       // Do nothing if cancel button is clicked
//       return;
//     }

//     if (!validateEmail(userEmailInput)) {
//       alert("Please enter a valid email address.");
//       event.preventDefault(); // Prevent form submission
//     }
//   });



  //handling geolocation and image watermark
  let pictureInput = document.getElementById("picture");
  let capturedImageDataInput = document.getElementById("capturedImageData");
  let cityInput = document.getElementById("city");

  pictureInput.addEventListener("change", function (event) {
    event.preventDefault();

    // Show loading overlay
    createLoadingOverlay();

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          const fileInput = pictureInput.files[0];

          if (fileInput) {
            const reader = new FileReader();

            reader.onload = function (e) {
              const latitude = position.coords.latitude;
              const longitude = position.coords.longitude;

              reverseGeocode(latitude, longitude, function (city) {
                cityInput.value = city;

                const img = new Image();
                img.onload = function () {
                  const relativeFontSize = 0.024;
                  const stampedDataURL = embedWatermark(
                    img,
                    latitude,
                    longitude,
                    city,
                    relativeFontSize
                  );
                  capturedImageDataInput.value = stampedDataURL;

                  // Remove the loading overlay after a short delay
                  setTimeout(function () {
                    removeLoadingOverlay();
                  }, 2000); 
                };
                img.src = e.target.result;
              });
            };

            reader.readAsDataURL(fileInput);
          } else {
            console.error("File input is null.");
            // Remove the loading overlay in case of an error
            removeLoadingOverlay();
          }
        },
        function (error) {
          console.error("Error getting geolocation:", error.message);
          // Remove the loading overlay in case of an error
          removeLoadingOverlay();
        }
      );
    } else {
      console.error("Geolocation is not supported by this browser.");
      // Remove the loading overlay in case geolocation is not supported
      removeLoadingOverlay();
    }
  });

  function createLoadingOverlay() {
    let overlay = document.createElement("div");
    overlay.className = "loading-overlay";
    overlay.innerHTML =
      '<div class="spinner"></div><p>Fetching location... Please wait.</p>';
    document.body.appendChild(overlay);
  }

  function removeLoadingOverlay() {
    let overlay = document.querySelector(".loading-overlay");
    if (overlay && overlay.parentNode) {
      overlay.parentNode.removeChild(overlay);
    }
  }
  function embedWatermark(img, latitude, longitude, city, relativeFontSize) {
    const canvas = document.createElement("canvas");
    const tempCtx = canvas.getContext("2d");

    canvas.width = img.width;
    canvas.height = img.height;
    tempCtx.drawImage(img, 0, 0);

    const fontSize = Math.min(canvas.width, canvas.height) * relativeFontSize;

    tempCtx.font = `${fontSize}px Helvetica`;
    tempCtx.fillStyle = "black";

    const timestamp = new Date().toLocaleString();

    const timestampX = 10;
    const timestampY = canvas.height - 100;

    // Display coordinates and city on separate lines
    const textLines = [
      `Latitude: ${latitude}`,
      `Longitude: ${longitude}`,
      `City: ${city}`,
      `Time: ${timestamp}`,
    ];

    // Draw the watermark and timestamp with line breaks
    textLines.forEach((line, index) => {
      tempCtx.fillText(line, 10, canvas.height - 20 - index * fontSize);
    });

    return canvas.toDataURL("image/png");
  }
  function reverseGeocode(latitude, longitude, callback) {
    // Use OpenCage for reverse geocoding
    let apiKey = "0fedc3f472644c6e9d37ed2ce5f9e030";
    let apiUrl = `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}`;

    fetch(apiUrl)
      .then((response) => response.json())
      .then((data) => {
        // Extract city information from the response
        let city = "Unknown";
        if (data.results && data.results.length > 0) {
          city = data.results[0].components.city || "Unknown";
        }

        callback(city);
      })
      .catch((error) => {
        console.error("Error in reverse geocoding:", error);
        callback("Unknown");
      });
  }
});
