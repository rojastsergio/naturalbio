/**
 * Script para corregir problemas con el ancho de elementos
 * en pantallas grandes y forzar el uso del ancho completo
 */
document.addEventListener('DOMContentLoaded', function() {
  // Función para expandir contenedores
  function expandContainers() {
    // Contenedores principales del layout
    const containers = document.querySelectorAll(
      '.container-app, .container, .max-w-7xl, .max-w-6xl, [class*="container"], [class*="max-w-"]'
    );
    
    containers.forEach(function(container) {
      container.style.width = '100%';
      container.style.maxWidth = '100%';
    });
    
    // Tablas y elementos que pueden causar scroll
    const tables = document.querySelectorAll('table, .table-responsive, .overflow-x-auto');
    tables.forEach(function(table) {
      table.style.width = '100%';
      table.style.maxWidth = '100%';
      table.style.overflowX = 'visible';
    });
    
    // Celdas de tabla
    const cells = document.querySelectorAll('td, th');
    cells.forEach(function(cell) {
      cell.style.whiteSpace = 'normal';
      cell.style.wordBreak = 'break-word';
    });
    
    // Calendarios FullCalendar
    const calendarElements = document.querySelectorAll(
      '.fc, .fc-view-container, .fc-view, .fc-view > table'
    );
    calendarElements.forEach(function(element) {
      element.style.width = '100%';
      element.style.maxWidth = '100%';
    });
    
    // Grids
    const grids = document.querySelectorAll('.grid');
    grids.forEach(function(grid) {
      grid.style.width = '100%';
      grid.style.maxWidth = '100%';
      // Definir columnas dinámicas para mejor aprovechamiento del espacio
      if (window.innerWidth >= 1280) {
        grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
      }
    });
  }
  
  // Ejecutar al cargar
  expandContainers();
  
  // Ejecutar cuando cambie el tamaño de la ventana
  window.addEventListener('resize', expandContainers);
  
  // Ejecutar cuando se haga clic (posiblemente trigger de cambios de vista)
  document.addEventListener('click', function() {
    setTimeout(expandContainers, 200);
  });
  
  // Ejecutar cada segundo para asegurar que se aplique después de cargas dinámicas
  setInterval(expandContainers, 1000);
});