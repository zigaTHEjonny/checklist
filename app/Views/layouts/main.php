<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?=$title?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
  </head>
<body class="dark-mode">
    
      <?php if (session()->get('isLoggedIn')): ?>
        <div class="hover-buttons">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <form action="/logout" method="post" style="display:inline;">
            <?= csrf_field() ?>
            <button type="submit" class="logout-btn">Logout</button>
        </form>


        <button id="darkModeToggle" class="darkmode-btn">☀️</button>

        <a class="export-btn" href="/export" target="_blank">Export</a>

        <button class="import-btn">Import</button>
        <input type="file" class="import-input" accept=".json" style="display: none;" />

      </div>
      </div>
        <?php endif; ?>
       


    

    <div class="container">

        <?= $this->renderSection('content') ?>

    </div>

    <script>
      const uncheckedList = document.getElementById('unchecked-list');
      const checkedList = document.getElementById('checked-list');
      const toggleButton = document.getElementById('toggle-completed');
      const input = document.getElementById('new-item-input');
      const addButton = document.getElementById('add-button');

      function updateToggleText() {
        toggleButton.textContent = checkedList.style.display === 'none'
          ? `Show Completed (${checkedList.children.length})`
          : `Hide Completed`;
      }

      function attachEvents(li) {
          const checkbox = li.querySelector('input[type="checkbox"]');
          const span = li.querySelector('span');
          const deleteBtn = li.querySelector('.delete-btn');

          
          checkbox.addEventListener('change', function () {
              handleCheckboxChange(this);
          });
          deleteBtn.addEventListener('click', function () {
              handleDeleteClick(this);
          });
      }

      function createListItem(text, id = 0) {
        const li = document.createElement('li');

        li.innerHTML = `
          <div class="left">
            <input class="checkbox_input" type="checkbox" id="${id}" />
            <label for="${id}"><span>${text}</span></label>
          </div>
          <button class="delete-btn">✕</button>
        `;

        attachEvents(li);
        return li;
      }

      function updateItemState(li) {
        console.log(li);
        const checkbox = li.querySelector('input[type="checkbox"]');
          const span = li.querySelector('span');
        const isChecked = checkbox.checked;
        span.classList.toggle('completed', isChecked);
        (isChecked ? checkedList : uncheckedList).appendChild(li);
        updateToggleText();
      }

      addButton.addEventListener('click', () => {

        const value = input.value.trim();
        if (!value) return;


        const formData = new FormData();
        formData.append('val', value);

        fetch('/add_item', {
            method: 'POST',
            body: formData
        }).then(res => res.json())
          .then(data => {
              console.log(data);
              if (data.success) {
                if (data.edited > 0) {
                  let checkbox = document.querySelector('input[type="checkbox"][id="' + data.edited + '"]');
                  checkbox.checked = false;

                  let li = checkbox.closest('li');
                  console.log('input[type="checkbox"][id="'+data.edited+'"]');
                  updateItemState(li) 
                } else {
                  const li = createListItem(value, data.id);
                  uncheckedList.appendChild(li);
                }
                
                input.value = '';
              }
          })
          .catch(err => console.error('Error:', err));

      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') addButton.click();
      });

      toggleButton.addEventListener('click', () => {
        const isHidden = checkedList.style.display === 'none';
        checkedList.style.display = isHidden ? 'block' : 'none';
        updateToggleText();
      });

   
      document.querySelectorAll('#unchecked-list li, #checked-list li').forEach(attachEvents);
      updateToggleText();

     


    


    document.querySelectorAll('.import-btn').forEach((button, index) => {
    const input = document.querySelectorAll('.import-input')[index];

    button.addEventListener('click', function () {
        input.click();
    });

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (file && file.type === "application/json") {
            const reader = new FileReader();
            reader.onload = function (e) {
                const data = e.target.result;
                console.log('Imported data:', data);
              
                fetch('/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    alert(result.message || "Import complete!");
                    if (result.success) location.reload();
                })
                .catch(err => {
                    console.error('Import failed:', err);
                    alert("Import failed.");
                });
            };
            reader.readAsText(file);
        } else {
            alert("Please select a valid JSON file.");
        }
    });
});



    function handleDeleteClick(button) {
        const taskId = button.dataset.id;


        
        const formData = new FormData();
        formData.append('id', taskId);

        fetch('/remove_item', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                button.closest('li')?.remove();
            }
        })
        .catch(err => console.error('Error:', err));
    }


    document.getElementById('delete-all-btn').addEventListener('click', function () {
      if (confirm('Are you sure you want to delete all items?')) {
          fetch('/remove_all_items', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
              },
              body: ''
          })
          .then(response => {
              if (response.ok) {
                  location.reload();
              } else {
                  alert('Failed to delete all items.');
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred.');
          });
      }
    });



    function handleCheckboxChange(checkbox) {
        const checkboxId = checkbox.id;
        const isChecked = checkbox.checked;

        fetch('/update_item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: checkboxId, checked: isChecked })
        })
        .then(res => res.json())
        .then(data => {
            const li = checkbox.closest('li');
            updateItemState(li);
        })
        .catch(err => console.error('Error:', err));
    }



    const toggleBtn = document.getElementById('darkModeToggle');

          
    if (localStorage.getItem('mode') === 'dark') {
      document.body.classList.add('dark-mode');
      document.body.classList.remove('light-mode');
      toggleBtn.textContent = '☀️';
    } else {
      document.body.classList.add('light-mode');
    }

    toggleBtn.addEventListener('click', () => {
      const isDark = document.body.classList.toggle('dark-mode');
      document.body.classList.toggle('light-mode', !isDark);
      toggleBtn.textContent = isDark ? '☀️' : '🌙';
      localStorage.setItem('mode', isDark ? 'dark' : 'light');
    });


    document.querySelectorAll('.checkbox_input').forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            handleCheckboxChange(this);
        });
    });



      document.querySelectorAll('.delete-btn').forEach((button) => {
        button.addEventListener('click', function () {
            handleDeleteClick(this);
        });
      });
    </script>

</body>
</html>