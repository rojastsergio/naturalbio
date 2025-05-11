/**
 * Script definitivo para corregir problemas de ancho en toda la aplicación
 * Ejecutado después de que el DOM esté listo y periódicamente
 */

(function() {
  // Función principal para corregir anchos
  function fixWidths() {
    // Contenedores principales
    const mainContainers = document.querySelectorAll(
      '.container, .container-app, [class*="container-"], [class*="max-w-"], .max-w-7xl'
    );
    
    mainContainers.forEach(container => {
      container.style.width = '100%';
      container.style.maxWidth = '100%';
      container.style.boxSizing = 'border-box';
    });
    
    // Tablas y elementos que pueden causar scroll
    const scrollableElements = document.querySelectorAll(
      'table, .table-responsive, .overflow-x-auto, .data-table'
    );
    
    scrollableElements.forEach(element => {
      element.style.width = '100%';
      element.style.maxWidth = '100%';
      element.style.overflowX = 'visible';
      
      if (element.tagName === 'TABLE') {
        element.style.tableLayout = 'auto';
      }
    });
    
    // Celdas de tabla
    const tableCells = document.querySelectorAll('td, th');
    tableCells.forEach(cell => {
      cell.style.whiteSpace = 'normal';
      cell.style.wordBreak = 'break-word';
    });
    
    // Elementos de FullCalendar
    const calendarElements = document.querySelectorAll(
      '.fc, .fc-view-container, .fc-view, .fc-view > table'
    );
    
    calendarElements.forEach(element => {
      element.style.width = '100%';
      element.style.maxWidth = '100%';
    });
    
    // Contenedores de grid
    const grids = document.querySelectorAll('.grid');
    grids.forEach(grid => {
      grid.style.width = '100%';
      grid.style.maxWidth = '100%';
      
      if (window.innerWidth >= 1280) {
        grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
      }
    });
    
    // Contenido principal
    const mainContent = document.querySelectorAll('.main-content, main, .flex-1');
    mainContent.forEach(content => {
      content.style.width = '100%';
      content.style.maxWidth = '100%';
      content.style.overflow = 'hidden';
    });
  }
  
  // Ejecutar cuando el DOM está listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fixWidths);
  } else {
    fixWidths();
  }
  
  // Ejecutar cuando cambia el tamaño de la ventana
  window.addEventListener('resize', fixWidths);
  
  // Ejecutar después de cambios en el DOM
  document.addEventListener('click', () => {
    setTimeout(fixWidths, 100);
  });
  
  // Ejecutar periódicamente para asegurar que se aplican los cambios
  setInterval(fixWidths, 1000);
})();