import { mount } from '@vue/test-utils'
import RoverForm from '../src/components/RoverForm.vue'

describe('RoverForm', () => {
  it('renderitza els camps', () => {
    const wrapper = mount(RoverForm)
    expect(wrapper.text()).toContain('X:')
    expect(wrapper.text()).toContain('Y:')
    expect(wrapper.text()).toContain('Comandes:')
  })
})
