export function buildPath(
  startX: number,
  startY: number,
  midX: number,
  endX: number,
  endY: number,
  radius: number
): string {
  const verticalDir = endY > startY ? radius : -radius;

  return [
    `M ${startX} ${startY}`,
    `L ${midX - radius} ${startY}`,
    `Q ${midX} ${startY} ${midX} ${startY + verticalDir}`,
    `L ${midX} ${endY - verticalDir}`,
    `Q ${midX} ${endY} ${midX + radius} ${endY}`,
    `L ${endX - 2} ${endY}`,
  ].join(" ");
}
