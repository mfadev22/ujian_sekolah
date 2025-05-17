<?php
require "./admin/config.php";

$totalSeconds = 60; // default jika data tidak ada
$result = $conn->query("SELECT menit, detik FROM pengaturan_waktu LIMIT 1");
if ($result && ($row = $result->fetch_assoc()))
{
    $menit = (int)$row["menit"];
    $detik = (int)$row["detik"];
    $totalSeconds = $menit * 60 + $detik;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Ujian Online</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center font-['Poppins']">
	<div class="max-w-2xl w-full bg-white shadow-xl p-6 rounded-xl relative">

		<h1 class="text-2xl font-bold mb-4 text-center">Ujian Online </h1>
		<p id="participant-name" class="text-center mb-4 text-gray-700"></p>
		<div id="nav-buttons" class="flex flex-wrap gap-2 justify-center mb-4"></div>
		<div id="quiz-box" class="space-y-4"></div>
		<div class="mt-6 text-center space-x-4">
			<button id="submit-btn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 hidden">Selesai & Lihat Hasil</button>
		</div>
		<div id="result" class="mt-6 hidden">
			<h2 class="text-xl font-semibold mb-2">Hasil Ujian</h2>
			<p id="score" class="mb-4"></p>
			<div id="review"></div>
		</div>
		<!-- Popup Konfirmasi Waktu Habis -->
		<div id="timeup-popup" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
			<div class="bg-white rounded-lg p-6 max-w-sm w-full text-center shadow-lg">
				<p class="mb-4 text-lg font-semibold">Waktu sudah habis!</p>
				<button id="timeup-submit-btn" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Oke Submit</button>
			</div>
		</div>
		<!-- Popup Konfirmasi Soal Belum Lengkap -->
		<div id="submit-confirm-popup" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
			<div class="bg-white rounded-lg p-6 max-w-sm w-full text-center shadow-lg">
				<p class="mb-4 text-lg font-semibold">Masih ada soal yang belum dijawab.</p>
				<p class="mb-4">Yakin ingin menyelesaikan ujian sekarang?</p>
				<div class="flex justify-center gap-4">
					<button id="continue-btn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Lanjutkan Isi Soal</button>
					<button id="force-submit-btn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Selesai & Submit</button>
				</div>
			</div>
		</div>
		<!-- Popup Konfirmasi Semua Soal Sudah Dijawab -->
		<div id="submit-all-confirm-popup" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
			<div class="bg-white rounded-lg p-6 max-w-sm w-full text-center shadow-lg">
				<p class="mb-4 text-lg font-semibold">Semua soal sudah dijawab.</p>
				<p class="mb-4">Yakin ingin menyelesaikan ujian sekarang?</p>
				<div class="flex justify-center gap-4">
					<button id="cancel-submit-btn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
					<button id="confirm-submit-btn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Selesai & Submit</button>
				</div>
			</div>
		</div>
		<div class="absolute top-4 right-4 bg-gray-200 px-3 py-1 rounded font-mono text-sm" id="timer">00:00</div>
	</div>
	<script>
	// const questions = [{
	// 	question: "Pahlawan yang memperjuangkan emansipasi wanita adalah ....",
	// 	options: ["Christina Martha", "Raden Ajeng Kartini", "Cut nyak Dien", "Fatmawati"],
	// 	answer: 1
	// }, {
	// 	question: "Teks proklamasi diketik oleh ....",
	// 	options: ["Sukarno", "Ahmad Subarjo", "Sayuti Melik", "Muh. Hatta"],
	// 	answer: 2
	// }, {
	// 	question: "Hari pahlawan diperingati setiap tanggal ...",
	// 	options: ["21 April", "17 Agustus", "20 Mei", "1 Juni"],
	// 	answer: 2
	// }];

	if (sessionStorage.getItem("statusPengerjaan") === "1") {
		document.body.innerHTML = `
		<div class="flex items-center justify-center min-h-screen bg-gray-100 p-4 text-center">
			<div class="bg-white p-6 rounded-xl shadow-md max-w-md w-full">
			<h1 class="text-2xl font-bold text-gray-800">
				Kamu sudah mengerjakan Ujian ini.
			</h1>
			<p class="mt-4 text-gray-600">
				Silahkan Kembali Dan Laporkan Kepada Guru Kamu Ya,
				<br>Semangat Terus Belajarnya Yaa.... 😊😊
			</p>
			<a href="./" class="inline-block mt-6 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
				Kembali
			</a>
			</div>
		</div>
		`;
	} else{

	fetch('./admin/soal.php')
	.then(response => response.json())
	.then(data => {
		const questions = data;
		// console.log(questions); // Bisa kamu tampilkan atau proses lebih lanjut

		let currentQuestion = 0;
		const userAnswers = new Array(questions.length).fill(null);
		const quizBox = document.getElementById("quiz-box");
		const resultBox = document.getElementById("result");
		const scoreBox = document.getElementById("score");
		const reviewBox = document.getElementById("review");
		const navButtons = document.getElementById("nav-buttons");
		const submitBtn = document.getElementById("submit-btn");
		const timeupPopup = document.getElementById("timeup-popup");
		const timeupSubmitBtn = document.getElementById("timeup-submit-btn");
		const submitConfirmPopup = document.getElementById("submit-confirm-popup");
		const continueBtn = document.getElementById("continue-btn");
		const forceSubmitBtn = document.getElementById("force-submit-btn");
		const submitAllConfirmPopup = document.getElementById("submit-all-confirm-popup");
		const cancelSubmitBtn = document.getElementById("cancel-submit-btn");
		const confirmSubmitBtn = document.getElementById("confirm-submit-btn");
		const timerDisplay = document.getElementById("timer");
		// Timer 10 detik untuk testing
		// let totalSeconds = <?php echo $totalSeconds; ?>;
		let timerInterval = null;

		// function startTimer() {
		// 	updateTimerDisplay();
		// 	timerInterval = setInterval(() => {
		// 		totalSeconds--;
		// 		updateTimerDisplay();
		// 		if(totalSeconds <= 0) {
		// 			clearInterval(timerInterval);
		// 			showTimeupPopup();
		// 		}
		// 	}, 1000);
		// }
		// let totalSeconds;
		let totalSeconds = localStorage.getItem('remainingTime') ? parseInt(localStorage.getItem('remainingTime')) : <?php echo $totalSeconds; ?>;

		function updateTimerDisplay() {
			const m = Math.floor(totalSeconds / 60).toString().padStart(2, "0");
			const s = (totalSeconds % 60).toString().padStart(2, "0");
			timerDisplay.textContent = `${m}:${s}`;
		}

		function startTimer() {
			// const timerElement = document.getElementById('timer');

			// const countdown = setInterval(() => {
			//     totalSeconds--;
			//     localStorage.setItem('remainingTime', totalSeconds);

			//     let minutes = Math.floor(totalSeconds / 60);
			//     let seconds = totalSeconds % 60;

			//     timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

			//     if (totalSeconds <= 0) {
			//         clearInterval(countdown);
			//         localStorage.removeItem('remainingTime');
			// 		showTimeupPopup();
			//         // Optionally: submit form or redirect
			//     }
			// }, 1000);
			updateTimerDisplay();
			timerInterval = setInterval(() => {
				totalSeconds--;
				localStorage.setItem('remainingTime', totalSeconds);
				updateTimerDisplay();
				if (totalSeconds <= 0) {
					clearInterval(timerInterval);
					localStorage.removeItem('remainingTime');
					showTimeupPopup();
				}
			}, 1000);
		}

		function updateTimerDisplay() {
			const m = Math.floor(totalSeconds / 60).toString().padStart(2, "0");
			const s = (totalSeconds % 60).toString().padStart(2, "0");
			timerDisplay.textContent = `${m}:${s}`;
		}

		function showTimeupPopup() {
			timeupPopup.classList.remove("hidden");
		}

		function hideTimeupPopup() {
			timeupPopup.classList.add("hidden");
		}

		function showSubmitConfirmPopup() {
			submitConfirmPopup.classList.remove("hidden");
		}

		function hideSubmitConfirmPopup() {
			submitConfirmPopup.classList.add("hidden");
		}

		function showSubmitAllConfirmPopup() {
			submitAllConfirmPopup.classList.remove("hidden");
		}

		function hideSubmitAllConfirmPopup() {
			submitAllConfirmPopup.classList.add("hidden");
		}

		function renderNavButtons() {
			navButtons.innerHTML = "";
			questions.forEach((_, i) => {
				const btn = document.createElement("button");
				btn.textContent = i + 1;
				btn.className = `w-10 h-10 rounded-full border font-semibold ${
			userAnswers[i] !== null
				? "bg-blue-500 text-white"
				: "bg-gray-200 text-gray-800"
			} hover:bg-blue-400`;
				btn.onclick = () => {
					currentQuestion = i;
					showQuestion(currentQuestion);
					hideSubmitConfirmPopup();
					hideSubmitAllConfirmPopup();
				};
				navButtons.appendChild(btn);
			});
		}

		function showQuestion(index) {
			const q = questions[index];
			quizBox.innerHTML = `
		<p class="font-medium">Soal ${index + 1} dari ${questions.length}:</p>
		<h2 class="text-lg font-semibold">${q.question}</h2>
		<div class="space-y-2">
		${q.options
			.map(
			(opt, i) => `
			<label class="block cursor-pointer">
			<input type="radio" name="option" value="${i}" class="mr-2" ${
				userAnswers[index] === i ? "checked" : ""
			} />
			${opt}
			</label>
		`
			)
			.join("")}
		</div>
		<div class="mt-4 text-right">
		${
			index < questions.length - 1
			? `<button id="next-btn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Next</button>`
			: ""
		}
		</div>`;
		// Tambahkan event listener tombol Next jika ada
		const nextBtn = document.getElementById("next-btn");
			if(nextBtn) {
				nextBtn.addEventListener("click", () => {
					currentQuestion++;
					showQuestion(currentQuestion);
					hideSubmitConfirmPopup();
					hideSubmitAllConfirmPopup();
				});
			}
		}

		function showResult() {
			let score = 0;
			reviewBox.innerHTML = "";
			questions.forEach((q, i) => {
				const isCorrect = userAnswers[i] == q.answer;
				if(isCorrect) score++;
				reviewBox.innerHTML += `
			
				<div class="mb-4">
					<p class="font-medium">${i + 1}. ${q.question}</p>
					<p>Jawaban kamu: 
						<span class="${
				isCorrect ? "text-green-600" : "text-red-600"
				}">${q.options[userAnswers[i]] || "-"}</span>
					</p>
					<p>Jawaban benar: 
						<strong>${q.options[q.answer]}</strong>
					</p>
				</div>
			`;
			});
			const percentage = (score / questions.length) * 100;
			// Grading dan komentar
			let grade, comment;
			if(percentage === 100) {
				grade = "A";
				comment = "Sangat Baik";
			} else if(percentage >= 80) {
				grade = "B";
				comment = "Baik";
			} else if(percentage >= 60) {
				grade = "C";
				comment = "Cukup";
			} else if(percentage >= 40) {
				grade = "D";
				comment = "Kurang";
			} else {
				grade = "E";
				comment = "Sangat Kurang";
			}
			scoreBox.textContent = `Nilai: ${score} dari ${questions.length} (${percentage.toFixed(
			2
		)}%) - Grade: ${grade} (${comment})`;
			resultBox.classList.remove("hidden");
			submitBtn.style.display = "none";
			quizBox.classList.add("hidden");
			navButtons.classList.add("hidden");

			// console.log(participantName);
			// console.log(percentage.toFixed(2));
			// console.log(reviewBox);
			const formData = new FormData();
			formData.append("nama", participantName);
			formData.append("nilai", percentage.toFixed(2));
			formData.append("jawaban", reviewBox.innerHTML);

			// console.log(formData);

			return fetch("./admin/simpan_hasil.php", {
				method: "POST",
				body: formData
			}).then(res => res.text())
			  .then(data => {
				console.log("Respon dari PHP:", data);
			});
		}

		function updateSubmitBtnVisibility() {
			const allAnswered = userAnswers.every((a) => a !== null);
			submitBtn.style.display = allAnswered ? "inline-block" : "none";
		}
		// Event delegation for radio buttons in quiz box
		quizBox.addEventListener("change", (e) => {
			if(e.target.name === "option") {
				userAnswers[currentQuestion] = parseInt(e.target.value);
				renderNavButtons();
				updateSubmitBtnVisibility();
			}
		});
		// Submit button click handler
		submitBtn.addEventListener("click", () => {
			const allAnswered = userAnswers.every((a) => a !== null);
			if(!allAnswered) {
				// Tampilkan popup konfirmasi "Masih ada soal yang belum dijawab"
				showSubmitConfirmPopup();
			} else {
				// Tampilkan popup konfirmasi "Semua soal sudah dijawab"
				showSubmitAllConfirmPopup();
			}
		});
		// Tombol-tombol popup "Masih ada soal yang belum dijawab"
		continueBtn.addEventListener("click", () => {
			hideSubmitConfirmPopup();
		});
		forceSubmitBtn.addEventListener("click", () => {
			hideSubmitConfirmPopup();
			clearInterval(timerInterval);
			showResult();
		});
		// Tombol-tombol popup "Semua soal sudah dijawab"
		cancelSubmitBtn.addEventListener("click", () => {
			hideSubmitAllConfirmPopup();
		});
		confirmSubmitBtn.addEventListener("click", () => {
			hideSubmitAllConfirmPopup();
			clearInterval(timerInterval);
			localStorage.removeItem('remainingTime');
			timerDisplay.textContent = "Selesai";
			timerDisplay.classList.add("text-green-500");
			sessionStorage.setItem("statusPengerjaan", "1");
			showResult();
		});
		// Tombol popup waktu habis
		timeupSubmitBtn.addEventListener("click", () => {
			hideTimeupPopup();
			localStorage.removeItem('remainingTime');
			timerDisplay.textContent = "Selesai";
			timerDisplay.classList.add("text-green-500");
			sessionStorage.setItem("statusPengerjaan", "1");
			showResult();
		});
		// Render awal
		function fromBase64UrlSafe(b64) {
			// Kembalikan ke Base64 normal
			b64 = b64.replace(/-/g, '+').replace(/_/g, '/');
			while (b64.length % 4) {
				b64 += '=';
			}
			return decodeURIComponent(escape(atob(b64)));
		}
		const urlParams = new URLSearchParams(window.location.search);
		const encodedName = urlParams.get("nama") || "";
		const participantName = encodedName ? fromBase64UrlSafe(encodedName) : "Peserta";
		document.getElementById("participant-name").textContent = `Peserta: ${participantName}`;

		renderNavButtons();
		showQuestion(currentQuestion);
		updateSubmitBtnVisibility();
		startTimer();
		});
	}
	</script>
</body>

</html>
