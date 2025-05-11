/**
 * Script para corregir problemas de tablas y scroll horizontal
 * Ejecutado automáticamente después de la carga de la página
 */

document.addEventListener('DOMContentLoaded', function() {
  // Función para ajustar tablas responsivas
  function fixTables() {
    // Buscar todas las tablas en la página
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
      // Asegurar ancho completo
      table.style.width = '100%';
      
      // Limitar ancho de celdas para prevenir desbordamiento
      const cells = table.querySelectorAll('td, th');
      cells.forEach(cell => {
        cell.style.maxWidth = '200px';
        cell.style.whiteSpace = 'normal';
        cell.style.wordBreak = 'break-word';
      });
      
      // Preservar columnas de acciones
      const lastColumnCells = table.querySelectorAll('tr > td:last-child, tr > th:last-child');
      lastColumnCells.forEach(cell => {
        cell.style.whiteSpace = 'nowrap';
      });
    });
    
    // Corregir contenedores de tablas
    const tableContainers = document.querySelectorAll('.table-responsive, .overflow-x-auto');
    tableContainers.forEach(container => {
      container.style.overflowX = 'visible';
      container.style.width = '100%';
    });
  }
  
  // Función para ajustar grids
  function fixGrids() {
    const grids = document.querySelectorAll('.grid');
    grids.forEach(grid => {
      grid.style.width = '100%';
      // Utilizar auto-fill para mejor distribución
      if (window.innerWidth >= 1280) {
        grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
      }
    });
  }
  
  // Función para ajustar calendarios
  function fixCalendars() {
    const calendars = document.querySelectorAll('.fc, .calendar-container');
    calendars.forEach(calendar => {
      calendar.style.width = '100%';
      const views = calendar.querySelectorAll('.fc-view, .fc-view-container, .fc-view > table');
      views.forEach(view => {
        view.style.width = '100%';
      });
    });
  }
  
  // Ejecutar correcciones
  fixTables();
  fixGrids();
  fixCalendars();
  
  // Ejecutar también cuando cambie el tamaño de la ventana
  window.addEventListener('resize', function() {
    fixTables();
    fixGrids();
    fixCalendars();
  });
  
  // Para aplicaciones SPA, observar cambios en el DOM
  const observer = new MutationObserver(function(mutations) {
    fixTables();
    fixGrids();
    fixCalendars();
  });
  
  // Observar cambios en el cuerpo del documento
  observer.observe(document.body, { 
    childList: true,
    subtree: true 
  });
});