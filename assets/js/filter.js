async function loadData() {
  const services = await fetch("data/services.json").then(r => r.json());
  const questions = await fetch("data/questions.json").then(r => r.json());

  window._services = services;
  buildQuestions(questions);
  renderServices(services);
}

function buildQuestions(questions) {
  const filters = document.getElementById("filters");

  questions.forEach(q => {
    const section = document.createElement("div");
    section.className = "filter-section";

    section.innerHTML = `<h3>${q.question}</h3>`;

    q.options.forEach(option => {
      const id = `${q.id}-${option}`;

      section.innerHTML += `
        <label class="filter-option">
          <input type="checkbox" name="${q.id}" value="${option}">
          ${option}
        </label>
      `;
    });

    filters.appendChild(section);
  });

  filters.addEventListener("change", applyFilters);
}

function applyFilters() {
  const selected = {};

  document.querySelectorAll('#filters input[type="checkbox"]:checked')
    .forEach(box => {
      selected[box.name] ||= [];
      selected[box.name].push(box.value);
    });

  const filtered = window._services.filter(service => {
    return Object.keys(selected).every(key => {
      if (!selected[key].length) return true;

      const value = service[key];

      if (Array.isArray(value)) {
        return selected[key].some(v => value.includes(v));
      }

      return selected[key].includes(value);
    });
  });

  renderServices(filtered);
}

function renderServices(services) {
  const container = document.getElementById("results");
  container.innerHTML = "";

  if (!services.length) {
    container.innerHTML = `<p>No matching services.</p>`;
    return;
  }

  services.forEach(s => {
    const card = document.createElement("div");
    card.className = "service-card";

    card.innerHTML = `
      <h2>${s.name}</h2>
      <p>${s.description}</p>
      <p><strong>Capacity:<strong>${s.capacity}</p>
    `;

    container.appendChild(card);
  });
}

loadData();
