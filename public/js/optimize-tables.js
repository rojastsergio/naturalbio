/**
 * Script para optimizar tablas y prevenir scroll horizontal
 */
document.addEventListener('DOMContentLoaded', function() {
  // Función para ajustar tablas responsivas
  function optimizeTables() {
    // Buscar todas las tablas en la página
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
      // Asegurar ancho completo
      table.style.width = '100%';
      
      // Permitir que el texto se ajuste en las celdas
      const cells = table.querySelectorAll('td, th');
      cells.forEach(cell => {
        cell.style.whiteSpace = 'normal';
        cell.style.wordBreak = 'break-word';
        // Limitar el ancho máximo de celdas muy anchas
        cell.style.maxWidth = '200px';
      });
    });
    
    // Asegurar que los contenedores de tablas no generen scroll
    const tableContainers = document.querySelectorAll('.table-responsive, .overflow-x-auto');
    tableContainers.forEach(container => {
      container.style.overflowX = 'visible';
      container.style.maxWidth = '100%';
    });
  }
  
  // Ejecutar una vez al cargar la página
  optimizeTables();
  
  // Ejecutar después de cargas Ajax o cambios en el DOM
  document.addEventListener('mousedown', function() {
    // Esperar a que se completen posibles actualizaciones
    setTimeout(optimizeTables, 200);
  });
});