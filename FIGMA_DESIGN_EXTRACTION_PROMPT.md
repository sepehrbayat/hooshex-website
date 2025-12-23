# Complete Figma Design Extraction and Implementation Prompt

## Objective
Extract the complete homepage design from Figma using MCP tools and implement it with 100% accuracy, matching every detail including spacing, colors, typography, positioning, and visual effects.

## Prerequisites
1. Ensure Figma MCP server is configured and running
2. Join the active Figma channel using `mcp_TalkToFigma_join_channel`
3. Have the Figma file open in Figma desktop app

## Step-by-Step Process

### Phase 1: Connect to Figma and Extract Design Structure

1. **Join Figma Channel**
   - Use `mcp_TalkToFigma_join_channel` with the active channel ID
   - Verify connection is successful

2. **Get Document Information**
   - Call `mcp_TalkToFigma_get_document_info` to understand the overall structure
   - Note the canvas dimensions, frame count, and layer hierarchy

3. **Read Complete Design**
   - Use `mcp_TalkToFigma_read_my_design` to get detailed information about all selected nodes
   - If selection is empty, scan the entire document by getting node IDs

4. **Extract All Sections**
   - Identify all major sections (Hero, Features, Career Paths, Super App, Popular Courses, AI Bot, Tools, Instagram, Testimonials, Blog, Contact, Banner)
   - For each section, get detailed node information using `mcp_TalkToFigma_get_node_info` or `mcp_TalkToFigma_get_nodes_info`

### Phase 2: Extract Design Specifications

For EACH section, extract the following details:

#### Layout & Positioning
- Exact `position` (absolute, relative, etc.)
- Precise `top`, `left`, `right`, `bottom` values in pixels
- `width` and `height` in pixels
- `z-index` if applicable
- `display` type (flex, grid, block)
- `flex-direction`, `justify-content`, `align-items`
- `gap` and `padding` values
- `margin` values

#### Colors & Backgrounds
- Background color (hex, rgba, gradients)
- Border colors and widths
- Text colors for all text elements
- Hover states colors
- Opacity values
- Background blur effects (`backdrop-filter`, `filter: blur()`)

#### Typography
- Font family (exact name)
- Font size in pixels
- Font weight (100-900)
- Line height in pixels
- Letter spacing
- Text alignment (left, right, center, justify)
- Text transform (uppercase, lowercase, capitalize)
- Font features (if any)

#### Visual Effects
- Border radius (exact pixel values)
- Box shadows (all shadow layers with exact values)
- Gradients (direction, colors, stops)
- Opacity values
- Transform properties (rotate, scale, translate)
- Filter effects (blur, brightness, etc.)

#### Images & Assets
- Image dimensions
- Image positioning
- Image sources/paths
- Image filters or overlays

#### Interactive Elements
- Button styles (all states: default, hover, active, disabled)
- Input field styles
- Form element styles
- Link styles

### Phase 3: Implementation Strategy

1. **Create Component Structure**
   - Map each Figma frame/component to a Blade component or section
   - Maintain exact hierarchy and nesting

2. **Implement with Tailwind CSS**
   - Use exact pixel values from Figma (use arbitrary values like `w-[758px]`, `h-[592px]`)
   - Match colors exactly (use hex values directly: `bg-[#EB55C8]`, `text-[#22165E]`)
   - Apply exact spacing: `gap-[24px]`, `p-[24px]`, `mt-[152px]`
   - Use exact border radius: `rounded-[32px]`, `rounded-[16px]`
   - Apply exact shadows: `shadow-[0px_2px_8px_rgba(235,85,200,0.46)]`

3. **Typography Implementation**
   - Use exact font sizes: `text-[32px]`, `text-[24px]`
   - Match line heights: `leading-[48px]`, `leading-[36px]`
   - Apply exact font weights: `font-bold` (700), `font-normal` (400)
   - Use exact colors: `text-[#22165E]`, `text-[#FCF1FB]`

4. **Positioning & Layout**
   - Use absolute positioning with exact values: `absolute left-0 top-[152px]`
   - Match flex/grid layouts exactly
   - Apply exact gaps and spacing

5. **Visual Effects**
   - Implement glass morphism: `bg-[rgba(224,224,224,0.16)] backdrop-blur-sm`
   - Apply exact blur effects: `blur-[100.05px]`
   - Match gradients exactly: `bg-gradient-to-br from-[#1C41B0] via-[#7B41C6] to-[#9F41DA]`
   - Apply exact shadows with all layers

### Phase 4: Section-by-Section Implementation

For each section, follow this process:

1. **Extract Section Node**
   ```
   - Get node ID for the section
   - Use mcp_TalkToFigma_get_node_info to get all properties
   - Note all child nodes
   ```

2. **Extract All Child Elements**
   ```
   - For each child node, get detailed info
   - Extract text content, images, buttons, inputs
   - Note exact positioning relative to parent
   ```

3. **Implement in Blade**
   ```
   - Create/update the Blade template
   - Apply exact Tailwind classes matching Figma specs
   - Use arbitrary values for exact pixel matches
   - Maintain RTL support where needed
   ```

4. **Verify Implementation**
   ```
   - Compare rendered output with Figma design
   - Check spacing, colors, typography
   - Verify responsive behavior matches Figma breakpoints
   ```

### Phase 5: Special Attention Areas

#### AI Bot Section (چه دوره ای مناسب منه؟)
- Glass chatbox: `bg-[rgba(224,224,224,0.16)] rounded-[32px]`
- Pink form background: `bg-[#EB55C8] rounded-[32px]`
- Exact positioning: chatbox at `top-[152px]`, form at `left-[381px]`
- Glass input fields: `bg-[rgba(224,224,224,0.16)] rounded-[16px]`
- Star icon gradient: `bg-gradient-to-br from-[#1C41B0] via-[#7B41C6] to-[#9F41DA]`
- Blur effects: `blur-[100.05px] opacity-52`

#### Hero Section
- Exact search bar styling
- Background blur effects
- Image positioning
- Typography hierarchy

#### Features Section
- Card dimensions and spacing
- Icon sizes and positioning
- Text alignment

#### Career Paths Section
- Grid layout
- Card styling with glass effect
- Image overlays

#### Popular Courses Section
- Course card dimensions
- Price display styling
- Badge positioning

#### All Other Sections
- Follow the same extraction and implementation process

### Phase 6: Quality Checklist

Before considering the implementation complete, verify:

- [ ] All sections match Figma design pixel-perfect
- [ ] Colors match exactly (use hex values, not theme colors)
- [ ] Typography matches (size, weight, line-height, font-family)
- [ ] Spacing matches exactly (padding, margin, gap)
- [ ] Positioning matches (absolute/relative, top/left values)
- [ ] Border radius matches exactly
- [ ] Shadows match all layers
- [ ] Gradients match direction and colors
- [ ] Blur effects match
- [ ] Opacity values match
- [ ] Images are correctly sized and positioned
- [ ] Interactive elements (buttons, inputs) match all states
- [ ] RTL layout is correct
- [ ] Responsive breakpoints match Figma mobile/tablet designs

### Phase 7: Code Quality

1. **Use Exact Values**
   - Don't approximate: use exact pixel values from Figma
   - Don't use theme colors if Figma specifies exact hex
   - Don't round spacing values

2. **Maintain Structure**
   - Keep the same HTML hierarchy as Figma layers
   - Preserve component nesting

3. **Comments**
   - Add comments indicating Figma node IDs or section names
   - Note any design decisions

4. **Responsive Design**
   - Extract mobile and tablet designs separately
   - Use Tailwind responsive prefixes correctly
   - Match Figma breakpoints exactly

## Execution Command

Execute this process systematically:

1. Start by joining the Figma channel
2. Extract document structure
3. For each section:
   - Get node information
   - Extract all design specs
   - Implement in Blade with exact Tailwind classes
   - Verify against Figma
4. Test on local server
5. Compare side-by-side with Figma
6. Make final adjustments

## Important Notes

- **Never approximate**: Always use exact values from Figma
- **Extract everything**: Don't skip any visual detail
- **Verify constantly**: Compare implementation with Figma frequently
- **Use arbitrary values**: Tailwind arbitrary values `[value]` for exact matches
- **Maintain RTL**: Ensure RTL layout works correctly
- **Test responsiveness**: Match mobile/tablet designs from Figma

## Expected Output

A complete homepage implementation that is 100% pixel-perfect match with the Figma design, including:
- All sections properly implemented
- Exact spacing, colors, and typography
- All visual effects (glass, blur, gradients, shadows)
- Correct positioning and layout
- Responsive design matching Figma breakpoints
- Interactive elements with correct states






